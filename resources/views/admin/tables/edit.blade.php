@extends('layouts.admin')
@section('title','Edit Meja')
@section('content')
<div class="card"><div class="card-body">
  <form method="post" action="{{ route('admin.tables.update',$table) }}">@csrf @method('PUT')
    <div class="form-row">
      <div class="form-group col-md-4"><label>Kode</label><input name="code" class="form-control" value="{{ $table->code }}" required></div>
      <div class="form-group col-md-4"><label>Nama</label><input name="name" class="form-control" value="{{ $table->name }}"></div>
      <div class="form-group col-md-4"><label>Kapasitas</label><input type="number" min="1" name="capacity" class="form-control" value="{{ $table->capacity }}" required></div>
    </div>
    <div class="form-group"><label>Status</label>
      <select name="status" class="form-control">
        @foreach(['available','occupied','reserved','disabled'] as $st)
          <option value="{{ $st }}" @selected($table->status===$st)>{{ $st }}</option>
        @endforeach
      </select>
    </div>
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('admin.tables.index') }}" class="btn btn-secondary">Kembali</a>
  </form>
</div></div>
@endsection
