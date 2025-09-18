@extends('layouts.public')
@section('title','Menu')

@push('head')
<style>
  .menu-toolbar {
    background: linear-gradient(180deg, rgba(232,213,196,.35), rgba(255,255,255,0));
    border: 1px solid rgba(107,79,79,.12);
    border-radius: 14px; padding:.75rem;
  }
  .product-card { transition: transform .18s ease, box-shadow .18s ease; }
  .product-card:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(0,0,0,.08); }
  .price del { color:#9b9b9b; font-size:.85rem; margin-left:.25rem; }
  .badge-off { position:absolute; top:.5rem; right:.5rem; background: var(--c-accent); color:#fff; border-radius:999px; padding:.2rem .5rem; font-size:.75rem; box-shadow: 0 6px 16px rgba(169,113,85,.25); }
  .cat-pill { display:inline-block; padding:.35rem .7rem; border-radius:999px; font-size:.85rem; background: var(--c-cream); color: var(--c-ink); text-decoration:none; margin:.15rem .25rem 0 0; }
  .cat-pill.active { background: var(--c-primary); color:#fff; }

  /* Anti layout shift untuk gambar */
  .ratio-4x3 { position:relative; width:100%; padding-top:75%; overflow:hidden; border-top-left-radius:14px; border-top-right-radius:14px; }
  .ratio-4x3 > img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
</style>
@endpush

@section('content')
  @php
    use Illuminate\Support\Str;
    $placeholder = asset('images/placeholder-600x400.webp');
    $publicFallback = 'https://upload.wikimedia.org/wikipedia/commons/1/14/No_Image_Available.jpg';
    $categorySlug = $categorySlug ?? request('category'); // guard kalau belum diset di controller
  @endphp

  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Menu</h1>
    @if (\Illuminate\Support\Facades\Route::has('cart.index'))
      <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-bag me-1"></i> Keranjang
      </a>
    @endif
  </div>

  {{-- Toolbar filter --}}
  <div class="menu-toolbar mb-3">
    <form class="row g-2 align-items-center" method="get" action="{{ \Illuminate\Support\Facades\Route::has('menu.index') ? route('menu.index') : url()->current() }}">
      <div class="col-12 col-md-6 col-lg-4">
        <select name="category" class="form-select" onchange="this.form.submit()">
          <option value="">Semua Kategori</option>
          @isset($categories)
            @foreach($categories as $c)
              <option value="{{ $c->slug }}" @selected($categorySlug===$c->slug)>{{ e($c->name) }}</option>
            @endforeach
          @endisset
        </select>
      </div>
      <div class="col-6 col-md-2">
        <button class="btn btn-primary w-100" type="submit">
          <i class="bi bi-funnel me-1"></i> Filter
        </button>
      </div>
      @if($categorySlug)
        <div class="col-6 col-md-2">
          <a class="btn btn-outline-secondary w-100" href="{{ \Illuminate\Support\Facades\Route::has('menu.index') ? route('menu.index') : url()->current() }}">
            Reset
          </a>
        </div>
      @endif

      <div class="col-12">
        {{-- Quick category pills --}}
        @isset($categories)
          @foreach($categories as $c)
            @php
              $pillUrl = \Illuminate\Support\Facades\Route::has('menu.index')
                ? route('menu.index', ['category' => $c->slug])
                : url()->current().'?category='.$c->slug;
            @endphp
            <a href="{{ $pillUrl }}" class="cat-pill {{ $categorySlug===$c->slug ? 'active' : '' }}">
              {{ e($c->name) }}
            </a>
          @endforeach
        @endisset
      </div>
    </form>
  </div>

  {{-- Grid produk --}}
  <div class="row g-3">
    @forelse($products as $p)
      @php
        $disk = $p->main_image_disk ?: 'public';
        $img  = $p->main_image_path
          ? \Illuminate\Support\Facades\Storage::disk($disk)->url($p->main_image_path)
          : $placeholder;
        $hasDisc    = !is_null($p->discount_price) && (float)$p->discount_price < (float)$p->price;
        $finalPrice = $hasDisc ? (float)$p->discount_price : (float)$p->price;
        $title      = $p->name ?? 'Produk';
        $detailUrl  = \Illuminate\Support\Facades\Route::has('menu.show') ? route('menu.show', $p->slug) : null;
        $canAdd     = \Illuminate\Support\Facades\Route::has('cart.add');
      @endphp

      <div class="col-6 col-md-4 col-lg-3">
        <div class="card product-card h-100">
          <div class="position-relative">
            <div class="ratio-4x3">
              <img
                src="{{ $img }}"
                @if($placeholder) onerror="this.onerror=null;this.src='{{ $placeholder }}';"
                @else onerror="this.onerror=null;this.src='{{ $publicFallback }}';" @endif
                alt="{{ e($title) }}"
                loading="lazy"
                width="600" height="450"
              >
            </div>
            @if($hasDisc)
              <span class="badge-off">Promo</span>
            @endif
          </div>

          <div class="card-body d-flex flex-column">
            <h6 class="card-title mb-1 text-truncate" title="{{ e($title) }}">{{ Str::limit($title, 60) }}</h6>

            <div class="mb-2 price">
              @if($hasDisc)
                <span class="text-danger fw-semibold">Rp {{ number_format($finalPrice,0,',','.') }}</span>
                <del>Rp {{ number_format((float)$p->price,0,',','.') }}</del>
              @else
                <span class="fw-semibold">Rp {{ number_format($finalPrice,0,',','.') }}</span>
              @endif
            </div>

            <div class="mt-auto d-grid gap-2">
              @if($detailUrl)
                <a href="{{ $detailUrl }}" class="btn btn-sm btn-outline-primary" aria-label="Detail {{ e($title) }}">
                  Detail
                </a>
              @endif

              @if($canAdd)
                <form action="{{ route('cart.add') }}" method="post" class="d-grid gap-2">@csrf
                  <input type="hidden" name="product_id" value="{{ $p->id }}">
                  <input type="hidden" name="qty" value="1">
                  <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Tambah
                  </button>
                </form>
              @else
                <button class="btn btn-sm btn-secondary" disabled>Tidak dapat menambah</button>
              @endif
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card">
          <div class="card-body text-center py-5">
            <div class="display-6 mb-2" style="opacity:.15">🍽️</div>
            <p class="mb-1">Tidak ada produk untuk kategori ini.</p>
            @if (\Illuminate\Support\Facades\Route::has('menu.index'))
              <a class="btn btn-outline-primary btn-sm mt-2" href="{{ route('menu.index') }}">Lihat semua menu</a>
            @endif
          </div>
        </div>
      </div>
    @endforelse
  </div>

  {{-- Paginasi --}}
  @php
    // Pastikan $products adalah paginator; kalau tidak, sembunyikan links() agar tidak error.
    $isPaginator = $products instanceof \Illuminate\Pagination\AbstractPaginator;
  @endphp

  @if($isPaginator)
    <div class="mt-3">
      {{-- withQueryString memastikan ?category=... tetap terbawa --}}
      {{-- {{ $products->withQueryString()->onEachSide(1)->links() }}
      Jika pakai Bootstrap 5 pagination view kustom: --}}
      {{ $products->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}

    </div>
  @endif
@endsection
