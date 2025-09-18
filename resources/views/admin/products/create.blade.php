@extends('layouts.admin')
@section('title','Tambah Produk')

@section('content')
<div class="card"><div class="card-body">
  <form method="post" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">@csrf
    <div class="form-row">
      <div class="form-group col-md-6"><label>Nama</label><input name="name" class="form-control" required></div>
      <div class="form-group col-md-6"><label>Slug (opsional)</label><input name="slug" class="form-control"></div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-4"><label>Kategori</label>
        <select name="category_id" class="form-control">
          <option value="">—</option>
          @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
        </select>
      </div>
      <div class="form-group col-md-4"><label>SKU (opsional)</label><input name="sku" class="form-control"></div>
      <div class="form-group col-md-4"><label>Stok</label><input type="number" name="stock" class="form-control" min="0" value="0" required></div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-4"><label>Harga</label><input type="number" step="0.01" name="price" class="form-control" required></div>
      <div class="form-group col-md-4"><label>Harga Diskon (opsional)</label><input type="number" step="0.01" name="discount_price" class="form-control"></div>
      <div class="form-group col-md-4">
        <label>Status</label>
        <div class="custom-control custom-switch mt-2">
<input type="hidden" name="is_active" value="0">
<input type="checkbox" name="is_active" class="custom-control-input" id="active" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>

          <label class="custom-control-label" for="active">Aktif</label>
        </div>
      </div>
    </div>
    <div class="form-group"><label>Deskripsi</label><textarea name="description" class="form-control" rows="3"></textarea></div>
    <div class="form-group"><label>Gambar Utama</label><input type="file" name="main_image" accept="image/*" class="form-control-file"></div>
    <div class="form-group"><label>Galeri (multi)</label><input type="file" name="gallery[]" accept="image/*" class="form-control-file" multiple></div>
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Batal</a>
  </form>
</div></div>
@endsection
