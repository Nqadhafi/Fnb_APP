@extends('layouts.public')
@section('title','Lupa Password')

@section('content')
  <div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h1 class="h4 mb-3">Lupa Password</h1>

          @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('status') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Email terdaftar</label>
              <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
              <div class="form-text">Kami akan mengirimkan link untuk reset password.</div>
            </div>
            <div class="d-grid gap-2">
              <button class="btn btn-primary" type="submit">Kirim Link Reset</button>
              <a class="btn btn-outline-secondary" href="{{ route('login') }}">Kembali ke Login</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
