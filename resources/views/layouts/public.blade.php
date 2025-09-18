<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', config('app.name'))</title>

  {{-- Google Fonts (CDN) --}}
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  {{-- Bootstrap 5 + Icons (CDN) --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root{
      /* Café palette */
      --c-bg: #faf6f2;
      --c-ink: #3b3230;
      --c-primary: #6b4f4f;       /* mocha */
      --c-primary-600:#5e4444;
      --c-primary-300:#a88e8e;
      --c-accent: #a97155;        /* caramel */
      --c-cream: #e8d5c4;         /* cream */
      --c-soft: #f3e7dc;

      /* Bootstrap variable overrides (lightly) */
      --bs-body-bg: var(--c-bg);
      --bs-body-color: var(--c-ink);
      --bs-link-color: var(--c-primary);
      --bs-link-hover-color: var(--c-primary-600);
    }
    html,body{ font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", sans-serif; }
    body { padding-top: 70px; }

    /* Navbar */
    .navbar-cozy{
      background: linear-gradient(180deg, var(--c-soft), rgba(255,255,255,0.85));
      backdrop-filter: blur(6px);
      border-bottom: 1px solid rgba(107,79,79,.15);
    }
    .navbar-cozy .nav-link{ color: var(--c-ink); opacity:.9; }
    .navbar-cozy .nav-link.active, .navbar-cozy .nav-link:hover{ color: var(--c-primary); }

    /* Buttons */
    .btn-primary{
      background-color: var(--c-primary) !important;
      border-color: var(--c-primary) !important;
    }
    .btn-primary:hover{ background-color: var(--c-primary-600) !important; border-color: var(--c-primary-600) !important; }
    .btn-outline-primary{ color: var(--c-primary) !important; border-color: var(--c-primary) !important; }
    .btn-outline-primary:hover{ background-color: var(--c-primary) !important; color:#fff !important; }

    /* Cards */
    .card{ border: 0; border-radius: 14px; box-shadow: 0 6px 20px rgba(107,79,79,.08); }
    .card-title{ font-weight: 600; }

    /* Badges */
    .badge.text-bg-secondary{ background-color: var(--c-primary-300) !important; }

    /* Alerts */
    .alert{ border: 0; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,.04); }

    /* Product card images */
    .product-card img { object-fit: cover; height: 180px; border-top-left-radius: 14px; border-top-right-radius: 14px; }

    /* Footer */
    footer{ background: linear-gradient(180deg, rgba(0,0,0,0), rgba(107,79,79,.05)); }

    /* Small utilities */
    .brand-mark{ font-weight: 700; letter-spacing:.4px; color: var(--c-primary); }
    .soft-chip{ background: var(--c-cream); color: var(--c-ink); border-radius: 999px; padding: .25rem .6rem; font-size: .825rem; }
  </style>

  @stack('head')
</head>
<body>

  {{-- Navbar --}}
  <nav class="navbar navbar-expand-lg fixed-top navbar-cozy">
    <div class="container">
      <a class="navbar-brand brand-mark" href="{{ route('home') }}">
        <i class="bi bi-cup-hot me-1"></i>{{ config('app.name', 'F&B App') }}
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div id="navMain" class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('menu.*') ? 'active' : '' }}" href="{{ route('menu.index') }}">Menu</a></li>
          @auth
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">Pesanan Saya</a></li>
          @endauth
        </ul>
        <ul class="navbar-nav ms-auto align-items-center">
          <li class="nav-item me-2">
            <a class="nav-link position-relative soft-chip" href="{{ route('cart.index') }}">
              <i class="bi bi-bag me-1"></i> Keranjang
            </a>
          </li>
          @auth
            <li class="nav-item">
              <form action="{{ route('logout') }}" method="POST" class="d-inline">@csrf
                <button class="btn btn-sm btn-outline-primary">Logout</button>
              </form>
            </li>
          @else
            <li class="nav-item"><a class="btn btn-sm btn-primary ms-2" href="{{ route('login') }}">Masuk</a></li>
          @endauth
        </ul>
      </div>
    </div>
  </nav>

  {{-- Flash --}}
  <div class="container mt-3">
    @if (session('ok'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('ok') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if ($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0">
          @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
  </div>

  {{-- Content --}}
  <main class="container mb-5">
    @yield('content')
  </main>

  {{-- Footer --}}
  <footer class="py-4 mt-4">
    <div class="container text-secondary small d-flex justify-content-between align-items-center">
      <span>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
      <span class="d-none d-md-inline">Brewed with <i class="bi bi-suit-heart-fill"></i> & coffee</span>
    </div>
  </footer>

  {{-- Scripts --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

  @stack('scripts')
</body>
</html>
