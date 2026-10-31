@extends('layouts.app')

@section('title', 'Barang')

@section('content')
    <header class="page-header">
        <div>
            <h1 class="page-title">Barang</h1>
            <p class="page-meta">{{ $items->total() }} terdaftar di katalog</p>
        </div>
        {{-- tombol tambah hanya untuk admin; proteksi asli di middleware route --}}
        @if (auth()->user()->isAdmin())
            <a href="{{ route('items.create') }}" class="btn btn-primary">Tambah Barang</a>
        @endif
    </header>

    <form method="GET" action="{{ route('items.index') }}" class="filter-bar">
        <div class="field">
            <label for="search" class="label">Cari nama / SKU</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}" class="input"
                   placeholder="Kardus, PKG-001…">
        </div>
        <div class="field">
            <label for="category_id" class="label">Kategori</label>
            <select id="category_id" name="category_id" class="input">
                <option value="">Semua</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="actions">
            <button type="submit" class="btn btn-secondary">Filter</button>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th class="num">Stok</th>
                    <th class="num">Min. Stok</th>
                    <th class="num">Harga</th>
                    @if (auth()->user()->isAdmin())
                        <th class="num">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td class="mono">{{ $item->sku }}</td>
                        <td><strong class="font-medium">{{ $item->name }}</strong></td>
                        <td class="muted">{{ $item->category->name }}</td>
                        {{-- badge di slot kiri berlebar tetap, angka tetap sejajar kanan --}}
                        <td class="num">
                            @if ($item->isLowStock())
                                <span class="badge-low">Menipis</span>
                            @else
                                <span class="badge-slot"></span>
                            @endif<span class="mono">{{ $item->stock }}</span>
                        </td>
                        <td class="num muted"><span class="mono">{{ $item->min_stock }}</span></td>
                        <td class="num">Rp&nbsp;<span class="mono">{{ number_format($item->price, 0, ',', '.') }}</span></td>
                        @if (auth()->user()->isAdmin())
                            <td class="num whitespace-nowrap">
                                <a href="{{ route('items.edit', $item) }}" class="link">Edit</a>
                                <form method="POST" action="{{ route('items.destroy', $item) }}"
                                      class="inline-block ml-3" data-confirm="Yakin hapus barang '{{ $item->name }}'?">
                                    {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}" class="text-center py-10 muted">
                            Tidak ada barang yang cocok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $items->links() }}
    </div>
@endsection
