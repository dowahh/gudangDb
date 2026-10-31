@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
    <header class="page-header">
        <div>
            <h1 class="page-title">Edit Supplier</h1>
            <p class="page-meta">{{ $supplier->stockTransactions()->count() }} transaksi pernah memakai supplier ini</p>
        </div>
        <div class="page-header-aside">
            <span class="page-date">{{ now()->format('d/m/Y') }}</span>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </header>

    <div class="kartu-label form-panel">
        <div class="kartu-label-header">Form Edit Supplier</div>

        <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="form-grid">
            {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="label">Nama Supplier</label>
                <input type="text" id="name" name="name" value="{{ old('name', $supplier->name) }}" required
                       class="input">
                @error('name')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="label">Telepon</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}"
                       class="input mono">
                @error('phone')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="address" class="label">Alamat</label>
                <textarea id="address" name="address" rows="3" class="input">{{ old('address', $supplier->address) }}</textarea>
                @error('address')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                <a href="{{ route('suppliers.index') }}" class="link">Batal</a>
            </div>
        </form>
    </div>
@endsection
