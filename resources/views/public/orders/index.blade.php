@extends('layouts.public')
@section('title','Pesanan Saya')

@push('head')
<style>
  .badge-pill { border-radius: 999px; padding:.35rem .6rem; font-weight:600; }
</style>
@endpush

@section('content')
  @php
    // Pastikan $orders adalah paginator agar index & links() aman
    $isPaginator = $orders instanceof \Illuminate\Pagination\AbstractPaginator;

    // Helper status -> badge
    function order_badge_class($status) {
      $s = strtolower((string)$status);
      return match($s) {
        'pending'   => 'text-bg-warning',
        'dibayar', 'paid' => 'text-bg-primary',
        'diproses','processing' => 'text-bg-info',
        'dikirim','shipped' => 'text-bg-info',
        'selesai','done','completed' => 'text-bg-success',
        'dibatalkan','canceled','cancelled' => 'text-bg-danger',
        default => 'text-bg-secondary',
      };
    }

    // Judul tipe pesanan
    function pretty_type($t) {
      return ucwords(str_replace('_',' ', (string)$t));
    }

    $menuUrl   = \Illuminate\Support\Facades\Route::has('menu.index') ? route('menu.index') : url('/');
    $detailOk  = \Illuminate\Support\Facades\Route::has('orders.show');
  @endphp

  <h1 class="h4 mb-3">Pesanan Saya</h1>

  @if(($isPaginator && $orders->total() === 0) || (!$isPaginator && $orders->isEmpty()))
    <div class="card border-0 bg-light-subtle">
      <div class="card-body d-flex align-items-center">
        <div class="me-2" style="opacity:.25; font-size:1.5rem">🧾</div>
        <div>Belum ada pesanan. <a href="{{ $menuUrl }}">Pesan sekarang</a>.</div>
      </div>
    </div>
  @else
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead class="table-light">
          <tr>
            <th style="width:64px">No</th>
            <th>Kode</th>
            <th>Tipe</th>
            <th>Status</th>
            <th class="text-end" style="width:160px">Total</th>
            <th style="width:180px">Tanggal</th>
            <th class="text-end" style="width:100px"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($orders as $idx => $o)
            @php
              $rowNo = $isPaginator ? ($orders->firstItem() + $idx) : ($idx + 1);
              $badge = order_badge_class($o->status);
            @endphp
            <tr>
              <td>{{ $rowNo }}</td>
              <td class="fw-semibold">{{ e($o->code) }}</td>
              <td>{{ pretty_type($o->order_type) }}</td>
              <td><span class="badge badge-pill {{ $badge }}">{{ e($o->status) }}</span></td>
              <td class="text-end">Rp {{ number_format((float)$o->grand_total,0,',','.') }}</td>
              <td>{{ optional($o->created_at)->format('d/m/Y H:i') }}</td>
              <td class="text-end">
                @if($detailOk)
                  <a href="{{ route('orders.show', $o) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if($isPaginator)
      <div class="mt-3">
        {{-- {{ $orders->withQueryString()->onEachSide(1)->links() }} --}}
        {{-- Jika pakai Bootstrap 5 pagination view: --}}
        {{ $orders->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
       
      </div>
    @endif
  @endif
@endsection
