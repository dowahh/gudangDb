@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Kategori</h1>
        <a href="{{ route('categories.create') }}"
           class="rounded bg-slate-800 text-white px-4 py-2 text-sm hover:bg-slate-700">
            Tambah Kategori
        </a>
    </div>

    <div class="bg-white rounded border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3 font-medium">Nama</th>
                    <th class="px-4 py-3 font-medium">Jumlah Barang</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-4 py-3">{{ $category->name }}</td>
                        <td class="px-4 py-3">{{ $category->items_count }}</td>
                        <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('categories.edit', $category) }}" class="text-slate-800 underline">Edit</a>
                            <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                  class="inline-block" data-confirm="Yakin hapus kategori '{{ $category->name }}'?">
                                {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-700 underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-500">Belum ada kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
@endsection
