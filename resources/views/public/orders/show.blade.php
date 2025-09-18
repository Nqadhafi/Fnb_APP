@extends('layouts.public')
@section('title','Detail Pesanan')

@push('head')
<style>
  .badge-pill { border-radius:999px; padding:.35rem .6rem; font-weight:600; }
</style>
@endpush

@section('content')
@php
  // Helper status -> badge
  function order_badge_class($status) {
    $s = strtolower((string)$status);
    return match($s) {
      'pending' => 'text-bg-warning',
      'dibayar','paid' => 'text-bg-primary',
      'diproses','processing' => 'text-bg-info',
      'dikirim','shipped' => 'text-bg-info',
      'selesai','done','completed' => 'text-bg-success',
      'dibatalkan','canceled','cancelled' => 'text-bg-danger',
      default => 'text-bg-secondary',
    };
  }
  function pretty_type($t) { return ucwords(str_replace('_',' ', (string)$t)); }

  $backUrl = \Illuminate\Support\Facades\Route::has('orders.index') ? route('orders.index') : url('/');

  $statusBadge = order_badge_class($order->status);
  $paidAt      = optional($order->paid_at)->format('d/m/Y H:i');
@endphp

<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h5 mb-0">Detail Pesanan — {{ e($order->code) }}</h1>
  <a href="{{ $backUrl }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
</div>

<div class="row g-4">
  <div class="col-12 col-lg-8">
    <div class="card mb-3">
      <div class="card-body">
        <div class="d-flex flex-wrap gap-3">
          <div>
            <span class="text-secondary">Status:</span>
            <span class="badge badge-pill {{ $statusBadge }}">{{ e($order->status) }}</span>
          </div>
          <div><span class="text-secondary">Jenis:</span> {{ pretty_type($order->order_type) }}</div>
          @if($order->paid_at)
            <div><span class="text-secondary">Dibayar:</span> {{ $paidAt }}</div>
          @endif
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Item</h6>

        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th>Produk</th>
                <th class="text-center" style="width:100px">Qty</th>
                <th class="text-end" style="width:140px">Harga</th>
                <th class="text-end" style="width:160px">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @forelse($order->items as $i)
                <tr>
                  <td>
                    <div class="fw-semibold">{{ e($i->product_name) }}</div>
                    @if(!empty($i->notes))
                      <div class="small text-secondary">{{ e($i->notes) }}</div>
                    @endif
                  </td>
                  <td class="text-center">{{ (int)$i->qty }}</td>
                  <td class="text-end">Rp {{ number_format((float)$i->unit_price,0,',','.') }}</td>
                  <td class="text-end">Rp {{ number_format((float)$i->line_total,0,',','.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-secondary py-4">Tidak ada item pada pesanan ini.</td>
                </tr>
              @endforelse
            </tbody>
            <tfoot>
              <tr>
                <th colspan="3" class="text-end">Subtotal</th>
                <th class="text-end">Rp {{ number_format((float)$order->subtotal,0,',','.') }}</th>
              </tr>
              @if(($order->service_charge ?? 0) > 0)
                <tr>
                  <th colspan="3" class="text-end">Service</th>
                  <th class="text-end">Rp {{ number_format((float)$order->service_charge,0,',','.') }}</th>
                </tr>
              @endif
              @if(($order->tax_total ?? 0) > 0)
                <tr>
                  <th colspan="3" class="text-end">Pajak</th>
                  <th class="text-end">Rp {{ number_format((float)$order->tax_total,0,',','.') }}</th>
                </tr>
              @endif
              <tr>
                <th colspan="3" class="text-end">Total</th>
                <th class="text-end">Rp {{ number_format((float)$order->grand_total,0,',','.') }}</th>
              </tr>
            </tfoot>
          </table>
        </div>

        @if(!empty($order->notes))
          <div class="alert alert-secondary small mb-0">
            <strong>Catatan:</strong> {{ e($order->notes) }}
          </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    {{-- Seksi pembayaran user --}}
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Pembayaran</h6>
        <p class="mb-2">
          <span class="text-secondary">Metode:</span> {{ e($order->payment_method ?? '-') }}
        </p>

        {{-- Form upload bukti: hanya untuk transfer/e-wallet & belum paid --}}
        @if(in_array($order->payment_method, ['transfer','e-wallet'], true) && !$order->paid_at)
          @if(\Illuminate\Support\Facades\Route::has('orders.payments.upload'))
            <form action="{{ route('orders.payments.upload', $order) }}" method="post" enctype="multipart/form-data" class="mt-2">@csrf
              <div class="mb-2">
                <label class="form-label">Nominal Transfer</label>
                <input type="number" name="amount" step="0.01" min="0" value="{{ (float)$order->grand_total }}" class="form-control" required>
              </div>
              <div class="mb-2">
                <label class="form-label">Bukti Pembayaran (JPG/PNG/PDF, maks 2MB)</label>
                <input type="file" name="proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Catatan (opsional)</label>
                <textarea name="notes" rows="2" class="form-control"></textarea>
              </div>
              <button class="btn btn-primary w-100">
                <i class="bi bi-cloud-arrow-up me-1"></i> Upload Bukti
              </button>
            </form>
          @else
            <div class="alert alert-warning">Fitur upload bukti belum tersedia.</div>
          @endif
        @endif

        {{-- Riwayat pembayaran --}}
        @if(isset($order->payments) && $order->payments->isNotEmpty())
          <hr>
          <h6 class="mb-2">Riwayat Pembayaran</h6>
          <ul class="list-group list-group-flush">
            @foreach($order->payments as $p)
              @php
                $pStatus = strtolower((string)$p->status);
                $pBadge  = $pStatus === 'verified' ? 'success' : ($pStatus === 'rejected' ? 'danger' : 'secondary');

                $proofUrl = null;
                if(!empty($p->proof_path)) {
                  $proofUrl = \Illuminate\Support\Facades\Storage::disk($p->proof_disk ?: 'public')->url($p->proof_path);
                }
              @endphp
              <li class="list-group-item px-0">
                <div class="d-flex justify-content-between align-items-start gap-3">
                  <div>
                    <div class="small text-secondary">
                      {{ strtoupper($p->method ?? '-') }}
                      — <span class="badge text-bg-{{ $pBadge }}">{{ e($p->status ?? '-') }}</span>
                    </div>
                    <div>Rp {{ number_format((float)$p->amount,0,',','.') }}</div>
                    @if(!empty($p->paid_at))
                      <div class="small text-secondary">{{ optional($p->paid_at)->format('d/m/Y H:i') }}</div>
                    @endif
                    @if(!empty($p->cash_received))
                      <div class="small">
                        Tunai diterima: Rp {{ number_format((float)$p->cash_received,0,',','.') }},
                        Kembalian: Rp {{ number_format((float)($p->change_given ?? 0),0,',','.') }}
                      </div>
                    @endif
                  </div>
                  <div class="text-end">
                    @if($proofUrl)
                      <a href="{{ $proofUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">Bukti</a>
                    @endif
                  </div>
                </div>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
