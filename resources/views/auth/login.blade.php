@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="bg-white rounded border border-gray-200 p-6">
        <h1 class="text-lg font-semibold mb-1">Masuk</h1>
        <p class="text-sm text-gray-500 mb-5">Sistem Manajemen Inventaris Gudang</p>

        @if ($errors->any())
            <div class="mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium mb-1">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <button type="submit"
                    class="w-full rounded bg-slate-800 text-white px-4 py-2 text-sm hover:bg-slate-700">
                Masuk
            </button>
        </form>

        <p class="mt-4 text-sm text-gray-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-slate-800 underline">Daftar di sini</a>
        </p>
    </div>
@endsection
