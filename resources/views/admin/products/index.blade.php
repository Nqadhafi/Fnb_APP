@extends('layouts.admin')
@section('title','Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h5 mb-0">Produk</h1>
  <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i> Tambah</a>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table table-striped mb-0">
      <thead class="thead-light">
        <tr><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aktif</th><th class="text-right pr-3">Aksi</th></tr>
      </thead>
      <tbody>
        @foreach($rows as $p)
          @php
            $img = $p->main_image_path ? Storage::disk($p->main_image_disk ?: 'public')->url($p->main_image_path) : 'https://via.placeholder.com/120x90?text=No+Image';
          @endphp
          <tr>
            <td><img src="{{ $img }}" class="img-thumb" alt=""></td>
            <td class="font-weight-bold">{{ $p->name }}</td>
            <td>{{ $p->category->name ?? '-' }}</td>
            <td>Rp {{ number_format($p->discount_price ?? $p->price,0,',','.') }}</td>
            <td>{{ $p->stock }}</td>
            <td>{!! $p->is_active ? '<span class="badge badge-success">Ya</span>' : '<span class="badge badge-secondary">Tidak</span>' !!}</td>
            <td class="text-right pr-3">
              <a href="{{ route('admin.products.edit',$p) }}" class="btn btn-sm btn-outline-primary">Edit</a>
              <form action="{{ route('admin.products.destroy',$p) }}" method="post" class="d-inline" onsubmit="return confirm('Hapus produk?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Hapus</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $rows->links() }}</div>
</div>
@endsection
