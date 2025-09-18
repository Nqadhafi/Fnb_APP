<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Admin — '.config('app.name'))</title>

  {{-- AdminLTE 3.2 (Bootstrap 4) --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  {{-- Font Awesome --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
  @stack('head')
  <style>
    .table td, .table th { vertical-align: middle; }
    .img-thumb { width: 90px; height: 70px; object-fit: cover; border-radius: .25rem; }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  {{-- Navbar --}}
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <form action="{{ route('logout') }}" method="POST" class="mb-0">@csrf
          <button class="btn btn-sm btn-outline-danger">Logout</button>
        </form>
      </li>
    </ul>
  </nav>

  {{-- Sidebar --}}
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
      <span class="brand-text font-weight-light">{{ config('app.name') }} Admin</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
          <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard')?'active':'' }}"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*')?'active':'' }}"><i class="nav-icon fas fa-folder-open"></i><p>Kategori</p></a></li>
          <li class="nav-item"><a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*')?'active':'' }}"><i class="nav-icon fas fa-hamburger"></i><p>Produk</p></a></li>
          <li class="nav-item"><a href="{{ route('admin.tables.index') }}" class="nav-link {{ request()->routeIs('admin.tables.*')?'active':'' }}"><i class="nav-icon fas fa-chair"></i><p>Meja</p></a></li>
          <li class="nav-item"><a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*')?'active':'' }}"><i class="nav-icon fas fa-receipt"></i><p>Pesanan / POS</p></a></li>
          <li class="nav-item"><a href="{{ route('admin.pos.index') }}" class="nav-link {{ request()->routeIs('admin.pos.*')?'active':'' }}"><i class="nav-icon fas fa-cash-register"></i><p>POS Sessions</p></a></li>
          <li class="nav-item"><a href="{{ route('admin.reports.sales') }}" class="nav-link {{ request()->routeIs('admin.reports.*')?'active':'' }}"><i class="nav-icon fas fa-chart-line"></i><p>Laporan</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

  {{-- Content --}}
  <div class="content-wrapper">
    <section class="content pt-3">
      <div class="container-fluid">
        @if (session('ok'))
          <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-1"></i> {{ session('ok') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
          </div>
        @endif
        @if ($errors->any())
          <div class="alert alert-danger alert-dismissible fade show">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0">
              @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
          </div>
        @endif

        @yield('content')
      </div>
    </section>
  </div>

  <footer class="main-footer small">
    <strong>© {{ date('Y') }} {{ config('app.name') }}</strong>
  </footer>
</div>

{{-- Scripts: jQuery + Bootstrap 4 + AdminLTE --}}
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
@stack('scripts')
</body>
</html>
