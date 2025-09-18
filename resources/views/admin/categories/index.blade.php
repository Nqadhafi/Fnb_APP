@extends('layouts.admin')
@section('title','Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h5 mb-0">Kategori</h1>
  <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i> Tambah</a>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover mb-0">
      <thead class="thead-light"><tr><th>Nama</th><th>Slug</th><th>Aktif</th><th class="text-right pr-3">Aksi</th></tr></thead>
      <tbody>
        @foreach($rows as $r)
          <tr>
            <td>{{ $r->name }}</td>
            <td>{{ $r->slug }}</td>
            <td>{!! $r->is_active ? '<span class="badge badge-success">Ya</span>' : '<span class="badge badge-secondary">Tidak</span>' !!}</td>
            <td class="text-right pr-3">
              <a href="{{ route('admin.categories.edit',$r) }}" class="btn btn-sm btn-outline-primary">Edit</a>
              <form action="{{ route('admin.categories.destroy',$r) }}" method="post" class="d-inline" onsubmit="return confirm('Hapus kategori?')">
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
