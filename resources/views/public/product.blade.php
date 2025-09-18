@extends('layouts.public')
@section('title', e($product->name))

@push('head')
<style>
  .ratio-4x3 { position:relative; width:100%; padding-top:75%; overflow:hidden; border-radius: .5rem; }
  .ratio-4x3 > img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
  .thumb { width:96px; height:96px; object-fit:cover; border-radius:.4rem; cursor:pointer; border:2px solid transparent; }
  .thumb.active { border-color: var(--c-primary, #0d6efd); }
</style>
@endpush

@section('content')
@php
  use Illuminate\Support\Str;

  $placeholder = asset('images/placeholder-800x600.webp');
  $publicFallback = 'https://upload.wikimedia.org/wikipedia/commons/1/14/No_Image_Available.jpg';

  $disk = $product->main_image_disk ?: 'public';
  $mainSrc = $product->main_image_path
      ? \Illuminate\Support\Facades\Storage::disk($disk)->url($product->main_image_path)
      : $placeholder;

  $hasDisc = !is_null($product->discount_price) && (float)$product->discount_price < (float)$product->price;
  $finalPrice = $hasDisc ? (float)$product->discount_price : (float)$product->price;
@endphp

<div class="row g-4">
  <div class="col-12 col-md-6">
    {{-- Gambar utama (anti layout shift) --}}
    <div class="ratio-4x3 mb-3">
      <img
        id="main-image"
        src="{{ $mainSrc }}"
        @if($placeholder) onerror="this.onerror=null;this.src='{{ $placeholder }}';"
        @else onerror="this.onerror=null;this.src='{{ $publicFallback }}';" @endif
        alt="{{ e($product->name) }}"
        loading="eager"
        width="800" height="600"
      >
    </div>

    {{-- Thumbnails --}}
    @if(isset($images) && $images->isNotEmpty())
      <div class="d-flex gap-2 flex-wrap" id="thumbs">
        @foreach($images as $idx => $img)
          @php
            $tDisk = $img->disk ?: 'public';
            $u = \Illuminate\Support\Facades\Storage::disk($tDisk)->url($img->path);
          @endphp
          <img
            src="{{ $u }}"
            alt="Thumbnail {{ $idx+1 }} {{ e($product->name) }}"
            class="thumb {{ $idx===0 ? 'active' : '' }}"
            loading="lazy"
            width="96" height="96"
            @if($placeholder) onerror="this.onerror=null;this.src='{{ $placeholder }}';"
            @else onerror="this.onerror=null;this.src='{{ $publicFallback }}';" @endif
            data-full="{{ $u }}"
          >
        @endforeach
      </div>
    @endif
  </div>

  <div class="col-12 col-md-6">
    <h1 class="h4">{{ e($product->name) }}</h1>

    <div class="mb-2">
      @if($hasDisc)
        <span class="h5 text-danger fw-semibold">Rp {{ number_format($finalPrice,0,',','.') }}</span>
        <del class="text-secondary">Rp {{ number_format((float)$product->price,0,',','.') }}</del>
      @else
        <span class="h5 fw-semibold">Rp {{ number_format($finalPrice,0,',','.') }}</span>
      @endif
    </div>

    @if(!empty($product->description))
      <p class="text-secondary">{{ strip_tags($product->description) }}</p>
    @endif

    {{-- Form tambah ke keranjang --}}
    @if (\Illuminate\Support\Facades\Route::has('cart.add'))
      <form action="{{ route('cart.add') }}" method="post" class="mt-3">@csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        {{-- opsi sederhana (bisa dikembangkan dari options_schema) --}}
        <div class="row g-2 align-items-center">
          <div class="col-auto"><label for="qty" class="col-form-label">Jumlah</label></div>
          <div class="col-auto">
            <input id="qty" name="qty" type="number" min="1" value="1" class="form-control" style="width:100px">
          </div>
        </div>
        <div class="mt-3 d-grid gap-2 d-md-flex">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-cart-plus me-1"></i> Tambah ke Keranjang
          </button>
          @if (\Illuminate\Support\Facades\Route::has('menu.index'))
            <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary">Kembali</a>
          @else
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Kembali</a>
          @endif
        </div>
      </form>
    @else
      <div class="alert alert-warning mt-3">Fitur keranjang belum tersedia.</div>
      @if (\Illuminate\Support\Facades\Route::has('menu.index'))
        <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary mt-2">Kembali</a>
      @endif
    @endif
  </div>
</div>

@push('scripts')
<script>
  // Ganti gambar utama saat thumbnail diklik
  (function(){
    const main = document.getElementById('main-image');
    const thumbs = document.getElementById('thumbs');
    if (!main || !thumbs) return;

    thumbs.addEventListener('click', function(e){
      const t = e.target.closest('img.thumb');
      if (!t) return;
      const src = t.getAttribute('data-full');
      if (!src) return;
      // swap
      main.src = src;
      // fallback onerror tetap aktif (sudah di-HTML)
      // set active state
      thumbs.querySelectorAll('.thumb').forEach(el => el.classList.remove('active'));
      t.classList.add('active');
    }, false);
  })();
</script>
@endpush
@endsection
