@extends('layouts.admin')
@section('title','Edit Kategori')
@section('content')
<div class="card"><div class="card-body">
  <form method="post" action="{{ route('admin.categories.update',$category) }}">@csrf @method('PUT')
    <div class="form-group"><label>Nama</label><input name="name" class="form-control" value="{{ old('name',$category->name) }}" required></div>
    <div class="form-group"><label>Slug</label><input name="slug" class="form-control" value="{{ old('slug',$category->slug) }}" required></div>
    <div class="form-group"><label>Deskripsi</label><textarea name="description" class="form-control" rows="3">{{ old('description',$category->description) }}</textarea></div>
    <div class="form-group"><div class="custom-control custom-switch">
      <input type="hidden" name="is_active" value="0">
<input type="checkbox" name="is_active" class="custom-control-input" id="active" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>

      <label for="active" class="custom-control-label">Aktif</label>
    </div></div>
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Kembali</a>
  </form>
</div></div>
@endsection
