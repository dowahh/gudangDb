@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-xl font-semibold mb-1">Dashboard</h1>
    <p class="text-sm text-gray-600 mb-6">
        Halo, <span class="font-medium">{{ auth()->user()->name }}</span>
        ({{ auth()->user()->role }})
    </p>

    {{-- Kartu statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Total Jenis Barang</p>
            <p class="text-2xl font-semibold mt-1">{{ $totalItems }}</p>
        </div>
        <div class="bg-white rounded border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Total Nilai Stok</p>
            <p class="text-2xl font-semibold mt-1">Rp {{ number_format($totalValue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Stok Menipis</p>
            <p class="text-2xl font-semibold mt-1 text-red-700">{{ $lowStockItems->count() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Grafik 7 hari --}}
        <div class="bg-white rounded border border-gray-200 p-4">
            <h2 class="text-sm font-semibold mb-3">Transaksi Masuk vs Keluar (7 Hari)</h2>
            {{-- data-url dibaca JS untuk fetch data chart dari endpoint JSON --}}
            <canvas id="transaction-chart" height="200"
                    data-url="{{ route('dashboard.chart') }}"></canvas>
        </div>

        {{-- Peringatan stok menipis --}}
        <div class="bg-white rounded border border-gray-200 p-4">
            <h2 class="text-sm font-semibold mb-3">Peringatan Stok Menipis</h2>
            @if ($lowStockItems->isEmpty())
                <p class="text-sm text-gray-500">Semua stok masih di atas minimum.</p>
            @else
                <ul class="divide-y divide-gray-100 text-sm">
                    @foreach ($lowStockItems as $item)
                        <li class="py-2 flex justify-between gap-2">
                            <span>{{ $item->name }} <span class="text-gray-400">({{ $item->sku }})</span></span>
                            <span class="whitespace-nowrap text-red-700">
                                {{ $item->stock }} / min {{ $item->min_stock }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- 5 transaksi terakhir --}}
    <div class="bg-white rounded border border-gray-200 mt-6 overflow-hidden">
        <h2 class="text-sm font-semibold px-4 pt-4 pb-2">5 Transaksi Terakhir</h2>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3 font-medium">Tanggal</th>
                    <th class="px-4 py-3 font-medium">Barang</th>
                    <th class="px-4 py-3 font-medium">Tipe</th>
                    <th class="px-4 py-3 font-medium">Jumlah</th>
                    <th class="px-4 py-3 font-medium">Oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($recentTransactions as $transaction)
                    <tr>
                        <td class="px-4 py-3">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $transaction->item->name }}</td>
                        <td class="px-4 py-3">{{ $transaction->type }}</td>
                        <td class="px-4 py-3">{{ $transaction->qty }}</td>
                        <td class="px-4 py-3">{{ $transaction->user->name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
