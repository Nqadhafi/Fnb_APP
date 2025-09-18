@extends('layouts.public')
@section('title','Beranda')

@push('head')
<style>
  .hero {
    background: radial-gradient(1200px 600px at 10% -10%, rgba(169,113,85,.20), transparent 60%),
                radial-gradient(900px 500px at 110% 10%, rgba(232,213,196,.45), transparent 50%),
                var(--c-soft);
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(107,79,79,.12);
  }
  .product-card { transition: transform .18s ease, box-shadow .18s ease; }
  .product-card:hover { transform: translateY(-4px); box-shadow: 0 12px 26px rgba(0,0,0,.08); }
  .price del { color:#9b9b9b; font-size:.9rem; margin-left:.25rem; }
  .chip-new { background: var(--c-cream); font-size:.75rem; padding:.2rem .5rem; border-radius: 999px; }
  /* menjaga rasio gambar dan mencegah CLS */
  .ratio-4x3 { position:relative; width:100%; padding-top:75%; overflow:hidden; }
  .ratio-4x3 > img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
</style>
@endpush

@section('content')
  @php
    use Illuminate\Support\Str;
    $placeholder = asset('images/placeholder-600x400.webp');
    // Jika asset placeholder tidak ada di public, fallback ke URL publik
    $publicFallback = 'https://upload.wikimedia.org/wikipedia/commons/1/14/No_Image_Available.jpg';
  @endphp

  {{-- HERO --}}
  <section class="hero p-4 p-md-5 mb-4">
    <div class="container py-3 py-md-4">
      <div class="row align-items-center">
        <div class="col-lg-8">
          <h1 class="display-6 mb-2">
            Selamat datang di <span class="brand-mark">{{ e(config('app.name')) }}</span>
          </h1>
          <p class="lead mb-4">
            Pesan makanan &amp; minuman favoritmu. Ambil nomor meja, login, lalu checkout — bayar di kasir atau transfer.
          </p>
          <div class="d-flex gap-2">
            @if (\Illuminate\Support\Facades\Route::has('menu.index'))
              <a class="btn btn-primary btn-lg" href="{{ route('menu.index') }}">
                <i class="bi bi-cup-hot me-1"></i> Lihat Menu
              </a>
            @endif

            @auth
              @if (\Illuminate\Support\Facades\Route::has('orders.index'))
                <a class="btn btn-outline-primary btn-lg" href="{{ route('orders.index') }}">
                  <i class="bi bi-receipt me-1"></i> Pesanan Saya
                </a>
              @endif
            @else
              @if (\Illuminate\Support\Facades\Route::has('login'))
                <a class="btn btn-outline-primary btn-lg" href="{{ route('login') }}">
                  <i class="bi bi-box-arrow-in-right me-1"></i> Masuk/Daftar
                </a>
              @endif
            @endauth
          </div>
        </div>
        <div class="col-lg-4 d-none d-lg-block text-center" aria-hidden="true">
          <i class="bi bi-emoji-smile" style="font-size:4rem; opacity:.15"></i>
        </div>
      </div>
    </div>
  </section>

  {{-- INFO MEJA TERPASANG (jika ada) --}}
  @isset($cart)
    @if($cart->table_session_id && optional($cart->tableSession)->table)
      <div class="alert alert-secondary" role="status">
        <i class="bi bi-geo-alt me-1"></i>
        Meja kamu: <strong>{{ e($cart->tableSession->table->code) }}</strong>. Siap melanjutkan pesanan.
      </div>
    @endif
  @endisset

  {{-- FEATURED --}}
  @isset($featured)
    <div class="d-flex align-items-center justify-content-between mb-2">
      <h2 class="h5 mb-0">Menu Terbaru</h2>
      @if (\Illuminate\Support\Facades\Route::has('menu.index'))
        <a class="small text-decoration-none" href="{{ route('menu.index') }}">
          Lihat semua <i class="bi bi-arrow-right-short"></i>
        </a>
      @endif
    </div>

    @if(collect($featured)->isNotEmpty())
      <div class="row g-3">
        @foreach($featured as $p)
          @php
            $disk = $p->main_image_disk ?: 'public';
            $src  = $p->main_image_path
              ? \Illuminate\Support\Facades\Storage::disk($disk)->url($p->main_image_path)
              : $placeholder;
            // title/alt aman
            $title = $p->name ?? 'Produk';
            $detailRoute = \Illuminate\Support\Facades\Route::has('menu.show') ? route('menu.show', $p->slug) : null;
          @endphp

          <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100">
              <div class="position-relative">
                <div class="ratio-4x3">
                  <img
                    src="{{ $src }}"
                    @if($placeholder) onerror="this.onerror=null;this.src='{{ $placeholder }}';"
                    @else onerror="this.onerror=null;this.src='{{ $publicFallback }}';" @endif
                    alt="{{ e($title) }}"
                    loading="lazy"
                    width="600" height="450"
                  >
                </div>
                <span class="position-absolute top-0 start-0 m-2 chip-new">Baru</span>
              </div>

              <div class="card-body d-flex flex-column">
                <h6 class="card-title mb-1 text-truncate" title="{{ e($title) }}">
                  {{ Str::limit($title, 60) }}
                </h6>

                <div class="mb-2 price">
                  @if(!empty($p->discount_price) && $p->discount_price < $p->price)
                    <span class="text-danger fw-semibold">
                      Rp {{ number_format((float)$p->discount_price, 0, ',', '.') }}
                    </span>
                    <del>Rp {{ number_format((float)$p->price, 0, ',', '.') }}</del>
                  @else
                    <span class="fw-semibold">
                      Rp {{ number_format((float)$p->price, 0, ',', '.') }}
                    </span>
                  @endif
                </div>

                @if($detailRoute)
                  <a href="{{ $detailRoute }}" class="btn btn-sm btn-outline-primary mt-auto" aria-label="Lihat detail {{ e($title) }}">
                    Detail
                  </a>
                @else
                  <span class="text-muted mt-auto small">Detail tidak tersedia</span>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="card border-0 bg-light-subtle">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-info-circle me-2"></i>
          <em>Belum ada produk.</em>
        </div>
      </div>
    @endif
  @endisset
@endsection
