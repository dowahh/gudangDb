@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')
    <h1 class="text-xl font-semibold mb-1">Laporan Stok</h1>
    <p class="text-sm text-gray-600 mb-4">Rekap seluruh barang dan total nilai gudang saat ini.</p>

    <div class="bg-white rounded border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3 font-medium">SKU</th>
                    <th class="px-4 py-3 font-medium">Nama</th>
                    <th class="px-4 py-3 font-medium">Kategori</th>
                    <th class="px-4 py-3 font-medium">Stok</th>
                    <th class="px-4 py-3 font-medium">Harga</th>
                    <th class="px-4 py-3 font-medium">Nilai Stok</th>
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
                        <td class="px-4 py-3">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($item->stock * $item->price, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada barang.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-gray-50 font-semibold">
                    <td colspan="5" class="px-4 py-3 text-right">Total Nilai Gudang</td>
                    <td class="px-4 py-3">Rp {{ number_format($totalValue, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection
