@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
    <div class="auth-card">
        <h1 class="auth-title">Masuk</h1>
        <p class="auth-sub">Sistem Inventaris Gudang</p>

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="auth-form">
            {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
            @csrf

            <div>
                <label for="email" class="label">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       autofocus class="input">
            </div>

            <div>
                <label for="password" class="label">Password</label>
                <input type="password" id="password" name="password" required class="input">
            </div>

            <div class="auth-actions">
                <button type="submit" class="btn btn-primary">Masuk ke gudang</button>
            </div>
        </form>

        <p class="auth-foot">
            Belum punya akun?
            <a href="{{ route('register') }}">Daftar</a>
        </p>
    </div>
@endsection
