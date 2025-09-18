@extends('layouts.public')
@section('title','Keranjang')

@push('head')
<style>
  @media (max-width: 576px) {
    .table thead th:nth-child(3),
    .table tbody td:nth-child(3),
    .table tfoot th:nth-child(5) { white-space: nowrap; }
    .qty-input { width: 80px; }
  }
  .qty-input { width: 96px; }
</style>
@endpush

@section('content')
  <h1 class="h4 mb-3">Keranjang</h1>

  @php
    $hasItems = $cart && isset($cart->items) && $cart->items->isNotEmpty();
    $grand    = $hasItems
      ? ($cart->grand_total ?? $cart->items->sum(fn($i) => (float)$i->unit_price * (int)$i->qty))
      : 0;
    $toMenuUrl = \Illuminate\Support\Facades\Route::has('menu.index') ? route('menu.index') : url('/');
  @endphp

  @if(!$hasItems)
    <div class="card border-0 bg-light-subtle">
      <div class="card-body d-flex align-items-center">
        <div class="me-2" style="opacity:.25; font-size:1.5rem">🧺</div>
        <div>
          Keranjang kosong.
          <a href="{{ $toMenuUrl }}">Lihat menu</a>.
        </div>
      </div>
    </div>
  @else
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead class="table-light">
          <tr>
            <th style="width:48px">No</th>
            <th>Produk</th>
            <th class="text-end" style="width:140px">Harga</th>
            <th class="text-center" style="width:170px">Qty</th>
            <th class="text-end" style="width:160px">Subtotal</th>
            <th class="text-end" style="width:60px"></th>
          </tr>
        </thead>
        <tbody>
          @php $no=1; @endphp
          @foreach($cart->items as $item)
            @php
              $subtotal = (float)$item->unit_price * (int)$item->qty;
              $updateable = \Illuminate\Support\Facades\Route::has('cart.item.update');
              $removable  = \Illuminate\Support\Facades\Route::has('cart.item.remove');
            @endphp
            <tr>
              <td>{{ $no++ }}</td>
              <td>
                <div class="fw-semibold">{{ e($item->product_name) }}</div>
                @if(!empty($item->notes))
                  <div class="small text-secondary">{{ e($item->notes) }}</div>
                @endif
              </td>
              <td class="text-end">Rp {{ number_format((float)$item->unit_price,0,',','.') }}</td>
              <td class="text-center">
                @if($updateable)
                  <form action="{{ route('cart.item.update', $item) }}" method="post" class="d-inline-flex gap-2 align-items-center">@csrf
                    <input type="number" name="qty" min="1" value="{{ (int)$item->qty }}" class="form-control form-control-sm qty-input text-center">
                    <button class="btn btn-sm btn-outline-primary" type="submit">Ubah</button>
                  </form>
                @else
                  <span class="text-muted">{{ (int)$item->qty }}</span>
                @endif
              </td>
              <td class="text-end">Rp {{ number_format($subtotal,0,',','.') }}</td>
              <td class="text-end">
                @if($removable)
                  <form action="{{ route('cart.item.remove', $item) }}" method="post" onsubmit="return confirm('Hapus item ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" aria-label="Hapus item">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <th colspan="4" class="text-end">Total</th>
            <th class="text-end">Rp {{ number_format($grand,0,',','.') }}</th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="d-flex gap-2">
      @if(\Illuminate\Support\Facades\Route::has('cart.clear'))
        <form action="{{ route('cart.clear') }}" method="post" onsubmit="return confirm('Kosongkan keranjang?')">
          @csrf @method('DELETE')
          <button class="btn btn-outline-secondary" type="submit">Kosongkan</button>
        </form>
      @endif

      @php $checkoutUrl = \Illuminate\Support\Facades\Route::has('checkout.show') ? route('checkout.show') : null; @endphp

      @if($checkoutUrl)
        <a href="{{ $checkoutUrl }}" class="btn btn-primary ms-auto">
          <i class="bi bi-receipt me-1"></i> Lanjut Checkout
        </a>
      @else
        <span class="ms-auto"></span>
      @endif
    </div>
  @endif
@endsection
