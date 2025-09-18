@extends('layouts.public')
@section('title','Masuk')

@section('content')
  <div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h1 class="h4 mb-3">Masuk</h1>

          @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('status') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" name="remember" class="form-check-input" id="remember">
              <label for="remember" class="form-check-label">Ingat saya</label>
            </div>
            <div class="d-grid gap-2">
              <button class="btn btn-primary" type="submit">Masuk</button>
              <a class="btn btn-outline-secondary" href="{{ route('password.request') }}">Lupa password?</a>
            </div>
          </form>

          <hr>
          <p class="mb-0">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>.</p>
        </div>
      </div>
    </div>
  </div>
@endsection
