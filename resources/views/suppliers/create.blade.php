@extends('layouts.app')

@section('title', 'Tambah Supplier')

@section('content')
    <header class="page-header">
        <div>
            <h1 class="page-title">Tambah Supplier</h1>
            <p class="page-meta">Supplier terhubung ke transaksi barang masuk</p>
        </div>
        <div class="page-header-aside">
            <span class="page-date">{{ now()->format('d/m/Y') }}</span>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </header>

    <div class="kartu-label form-panel">
        <div class="kartu-label-header">Form Tambah Supplier</div>

        <form method="POST" action="{{ route('suppliers.store') }}" class="form-grid">
            {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
            @csrf

            <div>
                <label for="name" class="label">Nama Supplier</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="input">
                @error('name')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="label">Telepon</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="input mono">
                @error('phone')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="address" class="label">Alamat</label>
                <textarea id="address" name="address" rows="3" class="input">{{ old('address') }}</textarea>
                @error('address')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Tambah supplier</button>
                <a href="{{ route('suppliers.index') }}" class="link">Batal</a>
            </div>
        </form>
    </div>
@endsection
