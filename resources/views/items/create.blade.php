@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
    <header class="page-header">
        <div>
            <h1 class="page-title">Tambah Barang</h1>
            <p class="page-meta">Stok awal otomatis 0</p>
        </div>
        <div class="page-header-aside">
            <span class="page-date">{{ now()->format('d/m/Y') }}</span>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </header>

    <div class="kartu-label form-panel">
        <div class="kartu-label-header">Form Tambah Barang</div>

        <form method="POST" action="{{ route('items.store') }}" class="form-grid">
            {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
            @csrf

            <div>
                <label for="sku" class="label">SKU</label>
                <input type="text" id="sku" name="sku" value="{{ old('sku') }}" required class="input mono">
                @error('sku')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="name" class="label">Nama Barang</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="input">
                @error('name')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category_id" class="label">Kategori</label>
                <select id="category_id" name="category_id" required class="input">
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-row-2">
                <div>
                    <label for="min_stock" class="label">Stok Minimum</label>
                    <input type="number" id="min_stock" name="min_stock" value="{{ old('min_stock', 0) }}" min="0"
                           required class="input">
                    @error('min_stock')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="price" class="label">Harga (Rp)</label>
                    <input type="number" id="price" name="price" value="{{ old('price') }}" min="0" step="0.01"
                           required class="input">
                    @error('price')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <p class="hint-text">Stok awal otomatis 0. Perubahan stok lewat transaksi masuk/keluar.</p>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Tambah barang</button>
                <a href="{{ route('items.index') }}" class="link">Batal</a>
            </div>
        </form>
    </div>
@endsection
