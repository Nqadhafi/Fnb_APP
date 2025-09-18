@extends('layouts.admin')
@section('title','Tambah Meja')
@section('content')
<div class="card"><div class="card-body">
  <form method="post" action="{{ route('admin.tables.store') }}">@csrf
    <div class="form-row">
      <div class="form-group col-md-4"><label>Kode</label><input name="code" class="form-control" required></div>
      <div class="form-group col-md-4"><label>Nama (opsional)</label><input name="name" class="form-control"></div>
      <div class="form-group col-md-4"><label>Kapasitas</label><input type="number" min="1" name="capacity" class="form-control" value="2" required></div>
    </div>
    <div class="form-group"><label>Status</label>
      <select name="status" class="form-control">
        <option value="available">available</option>
        <option value="occupied">occupied</option>
        <option value="reserved">reserved</option>
        <option value="disabled">disabled</option>
      </select>
    </div>
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('admin.tables.index') }}" class="btn btn-secondary">Batal</a>
  </form>
</div></div>
@endsection
