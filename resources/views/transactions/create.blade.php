@extends('layouts.app')

@section('title', 'Catat Transaksi')

@section('content')
    <header class="page-header">
        <div>
            <h1 class="page-title">Catat Transaksi Stok</h1>
            <p class="page-meta">Masuk menambah stok · Keluar mengurangi stok</p>
        </div>
        <div class="page-header-aside">
            <span class="page-date">{{ now()->format('d/m/Y') }}</span>
            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </header>

    <div class="kartu-label form-panel">
        <div class="kartu-label-header">Form Catat Transaksi</div>

        <form method="POST" action="{{ route('transactions.store') }}" class="form-grid">
            {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
            @csrf

            <div>
                <label for="item-search" class="label">Barang</label>
                {{-- live search: ketik nama/SKU, pilih dari daftar hasil (JS fetch ke /items/search) --}}
                <div class="search-wrap">
                    <input type="text" id="item-search" autocomplete="off" placeholder="Ketik nama atau SKU..."
                           value="" class="input">
                    {{-- item_id diisi JS saat user memilih dari hasil search --}}
                    <input type="hidden" id="item_id" name="item_id" value="{{ old('item_id') }}">
                    <ul id="item-search-results" class="search-results" hidden></ul>
                </div>
                <p id="item-selected-info" class="hint-text">
                    @if (old('item_id'))
                        Barang terpilih (id {{ old('item_id') }}) — cari ulang untuk melihat detail.
                    @endif
                </p>
                {{-- stok barang terpilih, ditampilkan JS di bawah input --}}
                <p id="item-stock" class="hint-text mono" hidden></p>
                <button type="button" id="item-clear" class="link link-sm">Pilih barang lain</button>
                @error('item_id')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <span class="label">Jenis</span>
                {{-- tombol besar MASUK/KELUAR; nilai disimpan di input tersembunyi untuk server --}}
                <input type="hidden" id="type" name="type" value="{{ old('type', 'masuk') }}">
                <div class="tipe-toggle" id="tipe-toggle">
                    <button type="button" class="btn tipe-btn" data-type="masuk">Masuk</button>
                    <button type="button" class="btn tipe-btn" data-type="keluar">Keluar</button>
                </div>
                @error('type')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-row-2">
                <div>
                    <label for="qty" class="label">Jumlah</label>
                    <input type="number" id="qty" name="qty" value="{{ old('qty') }}" min="1" required class="input">
                    <p class="hint-text">Keluar ditolak bila jumlah melebihi stok.</p>
                    @error('qty')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="transaction_date" class="label">Tanggal</label>
                    <input type="date" id="transaction_date" name="transaction_date"
                           value="{{ old('transaction_date', now()->toDateString()) }}" required class="input">
                    @error('transaction_date')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- field supplier hanya tampil untuk transaksi masuk (di-toggle JS) --}}
            <div id="supplier-field" @hidden(old('type', 'masuk') === 'keluar')>
                <label for="supplier_id" class="label">Supplier (opsional)</label>
                <select id="supplier_id" name="supplier_id" class="input">
                    <option value="">Tanpa supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
                <p class="hint-text">Kosongkan bila pembelian tanpa supplier.</p>
                @error('supplier_id')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="note" class="label">Catatan</label>
                <input type="text" id="note" name="note" value="{{ old('note') }}" class="input">
                @error('note')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Catat transaksi</button>
                <a href="{{ route('transactions.index') }}" class="link">Batal</a>
            </div>
        </form>
    </div>
@endsection
