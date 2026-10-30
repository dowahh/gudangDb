@extends('layouts.app')

@section('title', 'Barang')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Barang</h1>
        {{-- tombol tambah hanya untuk admin; proteksi asli di middleware route --}}
        @if (auth()->user()->isAdmin())
            <a href="{{ route('items.create') }}"
               class="rounded bg-slate-800 text-white px-4 py-2 text-sm hover:bg-slate-700">
                Tambah Barang
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('items.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
        <div>
            <label for="search" class="block text-sm font-medium mb-1">Cari (nama/SKU)</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}"
                   class="rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
        </div>
        <div>
            <label for="category_id" class="block text-sm font-medium mb-1">Kategori</label>
            <select id="category_id" name="category_id"
                    class="rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                <option value="">Semua</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded bg-slate-800 text-white px-4 py-2 text-sm hover:bg-slate-700">
                Filter
            </button>
            <a href="{{ route('items.index') }}" class="rounded border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                Reset
            </a>
        </div>
    </form>

    <div class="bg-white rounded border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3 font-medium">SKU</th>
                    <th class="px-4 py-3 font-medium">Nama</th>
                    <th class="px-4 py-3 font-medium">Kategori</th>
                    <th class="px-4 py-3 font-medium">Stok</th>
                    <th class="px-4 py-3 font-medium">Min. Stok</th>
                    <th class="px-4 py-3 font-medium">Harga</th>
                    @if (auth()->user()->isAdmin())
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($items as $item)
                    <tr>
                        <td class="px-4 py-3">{{ $item->sku }}</td>
                        <td class="px-4 py-3">{{ $item->name }}</td>
                        <td class="px-4 py-3">{{ $item->category->name }}</td>
                        <td class="px-4 py-3">
                            {{ $item->stock }}
                            @if ($item->isLowStock())
                                <span class="ml-1 rounded bg-red-50 border border-red-200 px-1.5 py-0.5 text-xs text-red-700">Menipis</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $item->min_stock }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        @if (auth()->user()->isAdmin())
                            <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('items.edit', $item) }}" class="text-slate-800 underline">Edit</a>
                                <form method="POST" action="{{ route('items.destroy', $item) }}"
                                      class="inline-block" data-confirm="Yakin hapus barang '{{ $item->name }}'?">
                                    {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-700 underline">Hapus</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}" class="px-4 py-6 text-center text-gray-500">
                            Tidak ada barang yang cocok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>
@endsection
