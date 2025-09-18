@extends('layouts.admin')
@section('title','Dashboard')

@section('content')
<div class="row">
  <div class="col-md-3">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>{{ $metrics['orders_today'] ?? 0 }}</h3>
        <p>Pesanan Hari Ini</p>
      </div>
      <div class="icon"><i class="fas fa-receipt"></i></div>
      <a href="{{ route('admin.orders.index') }}" class="small-box-footer">Lihat <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-md-3">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>Rp {{ number_format($metrics['revenue_today'] ?? 0,0,',','.') }}</h3>
        <p>Omzet Hari Ini</p>
      </div>
      <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>{{ $metrics['products'] ?? 0 }}</h3>
        <p>Produk</p>
      </div>
      <div class="icon"><i class="fas fa-hamburger"></i></div>
      <a href="{{ route('admin.products.index') }}" class="small-box-footer">Kelola <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-md-3">
    <div class="small-box bg-danger">
      <div class="inner">
        <h3>{{ $metrics['open_orders'] ?? 0 }}</h3>
        <p>Antrian Aktif</p>
      </div>
      <div class="icon"><i class="fas fa-concierge-bell"></i></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3 class="card-title">Pesanan Terbaru</h3></div>
  <div class="card-body p-0">
    <table class="table table-striped mb-0">
      <thead><tr><th>Kode</th><th>Status</th><th>Total</th><th>Tanggal</th><th class="text-right pr-3">Aksi</th></tr></thead>
      <tbody>
        @forelse($latestOrders as $o)
          <tr>
            <td>{{ $o->code }}</td>
            <td><span class="badge badge-secondary">{{ $o->status }}</span></td>
            <td>Rp {{ number_format($o->grand_total,0,',','.') }}</td>
            <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
            <td class="text-right pr-3">
              <a class="btn btn-xs btn-primary" href="{{ route('admin.orders.show',$o) }}">Detail</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center p-3"><em>Belum ada pesanan</em></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

  <div class="row">
    @foreach($tables as $t)
      @php
        switch ($t->status) {
          case 'available':
            $bg = 'bg-success'; $label = 'Tersedia'; break;
          case 'occupied':
            $bg = 'bg-danger'; $label = 'Terisi'; break;
          case 'reserved':
            $bg = 'bg-warning'; $label = 'Dipesan'; break;
          case 'disabled':
            $bg = 'bg-secondary'; $label = 'Nonaktif'; break;
          default:
            $bg = 'bg-light'; $label = ucfirst($t->status); break;
        }
      @endphp

      <div class="col-6 col-md-4 col-lg-3 mb-3">
        <div class="card text-white {{ $bg }} shadow-sm">
          <div class="card-body p-3">
            <h5 class="card-title mb-1">{{ $t->code }}</h5>
            <p class="mb-1">{{ $t->name }}</p>
            <small>Kap: {{ $t->capacity }}</small>
          </div>
          <div class="card-footer text-center py-1">
            <span class="badge bg-light text-dark">{{ $label }}</span>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endsection
