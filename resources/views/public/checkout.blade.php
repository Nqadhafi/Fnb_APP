@extends('layouts.public')
@section('title','Checkout')

@section('content')
  @php
    use Illuminate\Support\Str;

    $hasCart = isset($cart);
    $hasItems = $hasCart && isset($cart->items) && $cart->items->isNotEmpty();

    // hitung total fallback jika grand_total belum ada
    $grand = $hasItems
      ? ($cart->grand_total ?? $cart->items->sum(fn($i) => (float)$i->unit_price * (int)$i->qty))
      : 0;

    $tableSession = $hasCart ? optional($cart->tableSession) : null;
    $table        = optional($tableSession)->table;

    // koleksi aman untuk tab meja
    $activeSessions   = isset($activeTableSessions) ? collect($activeTableSessions) : collect();
    $availableTablesC = isset($availableTables) ? collect($availableTables) : collect();
  @endphp

  <h1 class="h4 mb-3">Checkout</h1>

  @if(!$hasItems)
    <div class="card border-0 bg-light-subtle">
      <div class="card-body d-flex align-items-center">
        <div class="me-2" style="opacity:.25; font-size:1.5rem">🧺</div>
        <div>
          Keranjang kosong.
          @if(\Illuminate\Support\Facades\Route::has('menu.index'))
            <a href="{{ route('menu.index') }}">Lihat menu</a>.
          @endif
        </div>
      </div>
    </div>
  @else
    <div class="row g-4">
      {{-- RINGKASAN KERANJANG --}}
      <div class="col-12 col-lg-8">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Ringkasan Pesanan</h5>

            @if($cart->table_session_id && $table)
              <div class="alert alert-secondary" role="status">
                <i class="bi bi-geo-alt me-1"></i>
                Meja terpasang: <strong>{{ e($table->code) }}</strong>
              </div>
            @endif

            <div class="table-responsive">
              <table class="table table-sm align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Produk</th>
                    <th class="text-end" style="width:140px">Harga</th>
                    <th class="text-center" style="width:100px">Qty</th>
                    <th class="text-end" style="width:160px">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($cart->items as $i)
                    @php $sub = (float)$i->unit_price * (int)$i->qty; @endphp
                    <tr>
                      <td>
                        <div class="fw-semibold">{{ e($i->product_name) }}</div>
                        @if(!empty($i->notes))
                          <div class="small text-secondary">{{ e($i->notes) }}</div>
                        @endif
                      </td>
                      <td class="text-end">Rp {{ number_format((float)$i->unit_price,0,',','.') }}</td>
                      <td class="text-center">{{ (int)$i->qty }}</td>
                      <td class="text-end">Rp {{ number_format($sub,0,',','.') }}</td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="3" class="text-end">Total</th>
                    <th class="text-end">Rp {{ number_format($grand,0,',','.') }}</th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>

      {{-- PEMBAYARAN + LOGIKA MEJA --}}
      <div class="col-12 col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Pembayaran</h5>

            {{-- ====== BLOK MEJA (untuk dine-in) ====== --}}
            <div class="mb-3">
              <label class="form-label">Meja (khusus dine-in)</label>

              @if($table)
                <div class="input-group mb-2">
                  <input class="form-control" value="Terpasang: {{ e($table->code) }}" readonly>
                  <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#chooseTableCollapse">
                    Ganti / Atur Meja
                  </button>
                </div>
              @else
                <button class="btn btn-outline-primary btn-sm mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#chooseTableCollapse">
                  Pilih / Ambil Meja
                </button>
              @endif

              <div id="chooseTableCollapse" class="collapse">
                <ul class="nav nav-tabs small" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-sesi" role="tab">Pilih Sesi Aktif</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-claim" role="tab">Ambil Meja Sendiri</a>
                  </li>
                </ul>

                <div class="tab-content border-start border-end border-bottom p-3">
                  {{-- Tab 1: pilih dari sesi yang dibuka staff --}}
                  <div class="tab-pane fade show active" id="tab-sesi" role="tabpanel">
                    @if(\Illuminate\Support\Facades\Route::has('cart.setTable'))
                      <form action="{{ route('cart.setTable') }}" method="post" class="row g-2 align-items-end">@csrf
                        <div class="col-8">
                          <select name="table_session_id" class="form-select">
                            <option value="">— Pilih sesi meja aktif —</option>
                            @forelse($activeSessions as $ts)
                              <option value="{{ $ts->id }}" @selected($cart->table_session_id==$ts->id)>
                                {{ e(optional($ts->table)->code) ?: 'Meja ?' }}
                                — dibuka {{ optional($ts->opened_at)->format('H:i') ?? '-' }}
                              </option>
                            @empty
                              <option value="" disabled>(Belum ada sesi aktif)</option>
                            @endforelse
                          </select>
                        </div>
                        <div class="col-4">
                          <button class="btn btn-outline-primary w-100">Pasang</button>
                        </div>
                      </form>
                      <div class="form-text">Gunakan ini bila staff sudah membuka sesi untuk mejamu.</div>
                    @else
                      <div class="text-muted">Pengaturan meja belum tersedia.</div>
                    @endif
                  </div>

                  {{-- Tab 2: user membuka sesi meja sendiri --}}
                  <div class="tab-pane fade" id="tab-claim" role="tabpanel">
                    @if(\Illuminate\Support\Facades\Route::has('cart.claimTable'))
                      <form action="{{ route('cart.claimTable') }}" method="post" class="row g-2 align-items-end">@csrf
                        <div class="col-6">
                          <label class="form-label mb-1">Pilih Meja Tersedia</label>
                          <select name="table_id" class="form-select" required>
                            <option value="">— Meja tersedia —</option>
                            @forelse($availableTablesC as $t)
                              <option value="{{ $t->id }}">{{ e($t->code) }} (kap {{ (int)$t->capacity }})</option>
                            @empty
                              <option value="" disabled>(Tidak ada meja tersedia)</option>
                            @endforelse
                          </select>
                        </div>
                        <div class="col-3">
                          <label class="form-label mb-1">Tamu</label>
                          <input type="number" name="guest_count" min="1" max="12" value="1" class="form-control">
                        </div>
                        <div class="col-3">
                          <label class="form-label mb-1 d-block">&nbsp;</label>
                          <button class="btn btn-success w-100">Ambil Meja</button>
                        </div>
                      </form>
                      <div class="form-text">Meja akan terkunci untukmu; staff tetap bisa memantau dari POS.</div>
                    @else
                      <div class="text-muted">Fitur klaim meja belum tersedia.</div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            {{-- ====== END BLOK MEJA ====== --}}

            <hr>

            {{-- FORM CHECKOUT (SATU-SATUNYA FORM DI BAGIAN INI) --}}
            @if(\Illuminate\Support\Facades\Route::has('checkout.place'))
              <form action="{{ route('checkout.place') }}" method="post">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Tipe Pesanan</label>
                  <select name="order_type" class="form-select" required>
                    <option value="dine_in">Makan di Tempat</option>
                    <option value="takeaway">Bawa Pulang</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label">Metode Pembayaran</label>
                  <select name="payment_method" class="form-select" required>
                    <option value="cash">Tunai di Kasir</option>
                    <option value="transfer">Transfer</option>
                    <option value="e-wallet">E-Wallet</option>
                  </select>
                  <div class="form-text">Jika memilih transfer/e-wallet, upload bukti di halaman detail pesanan setelah ini.</div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Catatan (opsional)</label>
                  <textarea name="notes" class="form-control" rows="2" placeholder="Tanpa gula, sedikit pedas, dll."></textarea>
                </div>

                <button class="btn btn-primary w-100">
                  <i class="bi bi-bag-check me-1"></i> Buat Pesanan
                </button>
              </form>
            @else
              <div class="alert alert-warning">Fitur checkout belum tersedia.</div>
            @endif
          </div>
        </div>
      </div>
    </div>
  @endif
@endsection
