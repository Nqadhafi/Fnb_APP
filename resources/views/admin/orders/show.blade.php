@extends('layouts.admin')
@section('title','Detail Pesanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h5 mb-0">Pesanan {{ $order->code }}</h1>
  <div>
    <a href="{{ route('admin.orders.receipt',$order) }}" target="_blank" class="btn btn-secondary btn-sm"><i class="fas fa-print mr-1"></i> Cetak Struk</a>
  </div>
</div>
@if($order->tableSession && $order->tableSession->table)
  <div class="card mb-3">
    <div class="card-body py-2">
      <div class="d-flex align-items-center">
        <i class="fas fa-chair mr-2"></i>
        <div>
          <div><strong>Meja:</strong> {{ $order->tableSession->table->code }}</div>
          <div class="small text-muted">
            Sesi #{{ $order->table_session_id }}
            @if($order->tableSession->opened_at)
              — dibuka {{ $order->tableSession->opened_at->format('d/m H:i') }}
            @endif
            @if($order->tableSession->guest_count)
              — {{ $order->tableSession->guest_count }} tamu
            @endif
          </div>
        </div>
        @if(!$order->paid_at && $order->status!=='completed')
          <a href="{{ route('admin.tables.index') }}" class="btn btn-xs btn-outline-secondary ml-auto">
            Kelola Meja
          </a>
        @endif
      </div>
    </div>
  </div>
@endif

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <thead class="thead-light"><tr><th>Item</th><th class="text-center">Qty</th><th class="text-right">Harga</th><th class="text-right">Subtotal</th><th>Status</th><th></th></tr></thead>
          <tbody>
            @foreach($order->items as $it)
              <tr>
                <td>{{ $it->product_name }}<br>@if($it->notes)<small class="text-muted">{{ $it->notes }}</small>@endif</td>
                <td class="text-center">{{ $it->qty }}</td>
                <td class="text-right">Rp {{ number_format($it->unit_price,0,',','.') }}</td>
                <td class="text-right">Rp {{ number_format($it->line_total,0,',','.') }}</td>
                <td><span class="badge badge-info">{{ $it->prep_status }}</span></td>
                <td class="text-right">
                  <form class="form-inline" action="{{ route('admin.orders.items.prepstatus',$it) }}" method="post">@csrf
                    <select name="prep_status" class="form-control form-control-sm mr-1">
                      @foreach(['queued','preparing','ready','served','void'] as $ps)
                        <option value="{{ $ps }}" @selected($it->prep_status===$ps)>{{ $ps }}</option>
                      @endforeach
                    </select>
                    <button class="btn btn-sm btn-outline-primary">Ubah</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr><th colspan="3" class="text-right">Subtotal</th><th class="text-right">Rp {{ number_format($order->subtotal,0,',','.') }}</th><th colspan="2"></th></tr>
            @if($order->service_charge>0)<tr><th colspan="3" class="text-right">Service</th><th class="text-right">Rp {{ number_format($order->service_charge,0,',','.') }}</th><th colspan="2"></th></tr>@endif
            @if($order->tax_total>0)<tr><th colspan="3" class="text-right">Pajak</th><th class="text-right">Rp {{ number_format($order->tax_total,0,',','.') }}</th><th colspan="2"></th></tr>@endif
            <tr><th colspan="3" class="text-right">Total</th><th class="text-right">Rp {{ number_format($order->grand_total,0,',','.') }}</th><th colspan="2"></th></tr>
          </tfoot>
        </table>
      </div>
    </div>

    @if($order->notes)
      <div class="alert alert-secondary mt-3 mb-0"><strong>Catatan:</strong> {{ $order->notes }}</div>
    @endif
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-header"><strong>Pembayaran</strong></div>
      <div class="card-body">
        <p>Status: <span class="badge badge-{{ $order->paid_at ? 'success':'secondary' }}">{{ $order->paid_at ? 'paid' : 'unpaid' }}</span></p>
        <p>Metode: {{ $order->payment_method ?? '-' }}</p>

{{-- POS: Bayar Tunai --}}
@if(!$order->paid_at)
  <form action="{{ route('admin.orders.pay.cash',$order) }}" method="post" class="mb-3">@csrf
    <div class="form-group">
      <label>Uang Diterima (Tunai)</label>
      <input type="number" step="0.01" name="cash_received" class="form-control" min="0" value="{{ $order->grand_total }}">
    </div>

    <div class="form-group">
      <label>POS Session</label>
      <select name="pos_session_id" class="form-control">
        <option value="">— Pilih sesi kasir (opsional) —</option>
        @foreach($openSessions as $s)
          <option value="{{ $s->id }}" @selected(old('pos_session_id',$order->pos_session_id)===$s->id)>
            #{{ $s->id }} — {{ $s->counter_name ?? 'Counter' }} (dibuka {{ $s->opened_at?->format('d/m H:i') }})
          </option>
        @endforeach
      </select>
      <small class="form-text text-muted">
        Jika belum ada, buka sesi di <a href="{{ route('admin.pos.index') }}" target="_blank">POS Sessions</a>.
      </small>
      @error('pos_session_id')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div>

    <button class="btn btn-success btn-block">
      <i class="fas fa-money-bill-wave mr-1"></i> Proses Tunai
    </button>
  </form>
@endif


        {{-- Daftar Pembayaran + Verifikasi --}}
        @if($order->payments->isNotEmpty())
          <hr>
          <h6>Riwayat Pembayaran</h6>
          @foreach($order->payments as $p)
            <div class="border rounded p-2 mb-2">
              <div class="d-flex justify-content-between">
                <div>
                  <div><strong>{{ strtoupper($p->method ?? '-') }}</strong> — <span class="badge badge-{{ $p->status==='verified'?'success':($p->status==='rejected'?'danger':'secondary') }}">{{ $p->status }}</span></div>
                  <div>Jumlah: Rp {{ number_format($p->amount,0,',','.') }}</div>
                  @if($p->paid_at)<div class="text-muted small">{{ $p->paid_at->format('d/m/Y H:i') }}</div>@endif
                </div>
                @if($p->proof_path)
                  @php $u = Storage::disk($p->proof_disk ?: 'public')->url($p->proof_path); @endphp
                  <a href="{{ $u }}" target="_blank" class="btn btn-xs btn-outline-secondary">Bukti</a>
                @endif
              </div>

              @if($p->status==='pending')
                <form action="{{ route('admin.payments.verify',$p) }}" method="post" class="mt-2">@csrf
                  <div class="btn-group btn-group-sm">
                    <button name="approve" value="1" class="btn btn-success">Setujui</button>
                    <button name="approve" value="0" class="btn btn-danger">Tolak</button>
                  </div>
                </form>
              @endif
            </div>
          @endforeach
        @endif

        {{-- Update Status Order --}}
        <hr>
        <form action="{{ route('admin.orders.status.update',$order) }}" method="post">@csrf
          <div class="form-group">
            <label>Ubah Status</label>
            <select name="status" class="form-control">
              @foreach(['open','pending','paid','preparing','ready','served','completed','cancelled'] as $st)
                <option value="{{ $st }}" @selected($order->status===$st)>{{ $st }}</option>
              @endforeach
            </select>
          </div>
          <button class="btn btn-outline-primary btn-block">Simpan Status</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
