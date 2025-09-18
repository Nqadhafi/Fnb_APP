@extends('layouts.public')
@section('title','Reset Password')

@section('content')
  <div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h1 class="h4 mb-3">Reset Password</h1>

          <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" value="{{ old('email', $email ?? '') }}" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label">Password Baru</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Konfirmasi Password</label>
              <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <div class="d-grid gap-2">
              <button class="btn btn-primary" type="submit">Simpan Password</button>
              <a class="btn btn-outline-secondary" href="{{ route('login') }}">Kembali ke Login</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
