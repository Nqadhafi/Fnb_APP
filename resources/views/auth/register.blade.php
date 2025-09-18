@extends('layouts.public')
@section('title','Daftar')

@section('content')
  <div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h1 class="h4 mb-3">Daftar</h1>

          <form method="POST" action="{{ route('register.submit') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Nama</label>
              <input type="text" name="name" value="{{ old('name') }}" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">No. HP (opsional)</label>
              <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Konfirmasi Password</label>
              <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <div class="d-grid gap-2">
              <button class="btn btn-primary" type="submit">Buat Akun</button>
              <a class="btn btn-outline-secondary" href="{{ route('login') }}">Sudah punya akun? Masuk</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
