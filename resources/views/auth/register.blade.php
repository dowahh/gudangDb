@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
    <div class="auth-card">
        <h1 class="auth-title">Daftar</h1>
        <p class="auth-sub">Akun baru otomatis staff</p>

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="auth-form">
            {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
            @csrf

            <div>
                <label for="name" class="label">Nama</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       autofocus class="input">
            </div>

            <div>
                <label for="email" class="label">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="input">
            </div>

            <div>
                <label for="password" class="label">Password</label>
                <input type="password" id="password" name="password" required class="input">
                <p class="hint-text">Minimal 8 karakter.</p>
            </div>

            <div>
                <label for="password_confirmation" class="label">Ulangi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="input">
            </div>

            <div class="auth-actions">
                <button type="submit" class="btn btn-primary">Buat akun</button>
            </div>
        </form>

        <p class="auth-foot">
            Sudah punya akun?
            <a href="{{ route('login') }}">Masuk</a>
        </p>
    </div>
@endsection
