@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
    <header class="page-header">
        <div>
            <h1 class="page-title">Edit Kategori</h1>
            <p class="page-meta">{{ $category->items()->count() }} barang memakai kategori ini</p>
        </div>
        <div class="page-header-aside">
            <span class="page-date">{{ now()->format('d/m/Y') }}</span>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </header>

    <div class="kartu-label form-panel">
        <div class="kartu-label-header">Form Edit Kategori</div>

        <form method="POST" action="{{ route('categories.update', $category) }}" class="form-grid">
            {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="label">Nama Kategori</label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required
                       class="input">
                @error('name')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                <a href="{{ route('categories.index') }}" class="link">Batal</a>
            </div>
        </form>
    </div>
@endsection
