@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
    <div class="bg-white rounded border border-gray-200 p-6">
        <h1 class="text-lg font-semibold mb-1">Daftar Akun</h1>
        <p class="text-sm text-gray-500 mb-5">Akun baru otomatis berrole staff.</p>

        @if ($errors->any())
            <div class="mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium mb-1">Nama</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium mb-1">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter.</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium mb-1">Ulangi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <button type="submit"
                    class="w-full rounded bg-slate-800 text-white px-4 py-2 text-sm hover:bg-slate-700">
                Daftar
            </button>
        </form>

        <p class="mt-4 text-sm text-gray-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-slate-800 underline">Masuk di sini</a>
        </p>
    </div>
@endsection
