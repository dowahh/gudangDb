@extends('layouts.app')

@section('title', 'Tambah Supplier')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Tambah Supplier</h1>

    <div class="bg-white rounded border border-gray-200 p-6 max-w-lg">
        @if ($errors->any())
            <div class="mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-4">
            {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium mb-1">Nama Supplier</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium mb-1">Telepon</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                       class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div>
                <label for="address" class="block text-sm font-medium mb-1">Alamat</label>
                <textarea id="address" name="address" rows="3"
                          class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">{{ old('address') }}</textarea>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded bg-slate-800 text-white px-4 py-2 text-sm hover:bg-slate-700">
                    Simpan
                </button>
                <a href="{{ route('suppliers.index') }}" class="text-sm text-gray-600 underline">Batal</a>
            </div>
        </form>
    </div>
@endsection
