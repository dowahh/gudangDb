@extends('layouts.app')

@section('title', 'Supplier')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Supplier</h1>
        <a href="{{ route('suppliers.create') }}"
           class="rounded bg-slate-800 text-white px-4 py-2 text-sm hover:bg-slate-700">
            Tambah Supplier
        </a>
    </div>

    <div class="bg-white rounded border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3 font-medium">Nama</th>
                    <th class="px-4 py-3 font-medium">Telepon</th>
                    <th class="px-4 py-3 font-medium">Alamat</th>
                    <th class="px-4 py-3 font-medium">Transaksi</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td class="px-4 py-3">{{ $supplier->name }}</td>
                        <td class="px-4 py-3">{{ $supplier->phone ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $supplier->address ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $supplier->stock_transactions_count }}</td>
                        <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="text-slate-800 underline">Edit</a>
                            <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}"
                                  class="inline-block" data-confirm="Yakin hapus supplier '{{ $supplier->name }}'?">
                                {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-700 underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada supplier.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $suppliers->links() }}
    </div>
@endsection
