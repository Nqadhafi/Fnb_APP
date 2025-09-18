@extends('layouts.admin')
@section('title','Edit Produk')

@section('content')
<div class="card"><div class="card-body">
  <form method="post" action="{{ route('admin.products.update',$product) }}" enctype="multipart/form-data">@csrf @method('PUT')
    <div class="form-row">
      <div class="form-group col-md-6"><label>Nama</label><input name="name" class="form-control" value="{{ old('name',$product->name) }}" required></div>
      <div class="form-group col-md-6"><label>Slug</label><input name="slug" class="form-control" value="{{ old('slug',$product->slug) }}" required></div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-4"><label>Kategori</label>
        <select name="category_id" class="form-control">
          <option value="">—</option>
          @foreach($categories as $c)<option value="{{ $c->id }}" @selected($product->category_id==$c->id)>{{ $c->name }}</option>@endforeach
        </select>
      </div>
      <div class="form-group col-md-4"><label>SKU</label><input name="sku" class="form-control" value="{{ old('sku',$product->sku) }}"></div>
      <div class="form-group col-md-4"><label>Stok</label><input type="number" name="stock" class="form-control" min="0" value="{{ old('stock',$product->stock) }}" required></div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-4"><label>Harga</label><input type="number" step="0.01" name="price" class="form-control" value="{{ old('price',$product->price) }}" required></div>
      <div class="form-group col-md-4"><label>Harga Diskon</label><input type="number" step="0.01" name="discount_price" class="form-control" value="{{ old('discount_price',$product->discount_price) }}"></div>
      <div class="form-group col-md-4">
        <label>Status</label>
        <div class="custom-control custom-switch mt-2">
<input type="hidden" name="is_active" value="0">
<input type="checkbox" name="is_active" class="custom-control-input" id="active" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>

          <label class="custom-control-label" for="active">Aktif</label>
        </div>
      </div>
    </div>
    <div class="form-group"><label>Deskripsi</label><textarea name="description" class="form-control" rows="3">{{ old('description',$product->description) }}</textarea></div>
    <div class="form-group"><label>Gambar Utama</label><input type="file" name="main_image" accept="image/*" class="form-control-file"></div>
    <div class="form-group"><label>Tambah Galeri</label><input type="file" name="gallery[]" accept="image/*" class="form-control-file" multiple></div>

    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Kembali</a>
  </form>
</div></div>

@if($images->isNotEmpty())
<div class="card">
  <div class="card-header"><h3 class="card-title">Galeri</h3></div>
  <div class="card-body">
    <div class="row">
      @foreach($images as $img)
        @php $u = Storage::disk($img->disk ?: 'public')->url($img->path); @endphp
        <div class="col-md-3 mb-3">
          <div class="border rounded p-2 text-center">
            <img src="{{ $u }}" class="img-fluid mb-2" style="height:130px;object-fit:cover">
            <div class="d-flex justify-content-between">
              <form action="{{ route('admin.products.images.primary',[$product,$img]) }}" method="post">@csrf
                <button class="btn btn-xs btn-outline-success" {{ $img->is_primary?'disabled':'' }}>Primary</button>
              </form>
              <form action="{{ route('admin.products.images.destroy',$img) }}" method="post" onsubmit="return confirm('Hapus gambar ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-outline-danger">Hapus</button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endif
@endsection
