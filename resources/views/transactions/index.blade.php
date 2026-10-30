@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Riwayat Transaksi</h1>
        <a href="{{ route('transactions.create') }}"
           class="rounded bg-slate-800 text-white px-4 py-2 text-sm hover:bg-slate-700">
            Catat Transaksi
        </a>
    </div>

    @if (! auth()->user()->isAdmin())
        <p class="mb-4 text-sm text-gray-600">Anda hanya melihat transaksi yang Anda catat sendiri.</p>
    @endif

    <form method="GET" action="{{ route('transactions.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
        <div>
            <label for="type" class="block text-sm font-medium mb-1">Tipe</label>
            <select id="type" name="type"
                    class="rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                <option value="">Semua</option>
                <option value="masuk" @selected(request('type') === 'masuk')>Masuk</option>
                <option value="keluar" @selected(request('type') === 'keluar')>Keluar</option>
            </select>
        </div>
        <div>
            <label for="start_date" class="block text-sm font-medium mb-1">Dari Tanggal</label>
            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                   class="rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
        </div>
        <div>
            <label for="end_date" class="block text-sm font-medium mb-1">Sampai Tanggal</label>
            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                   class="rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded bg-slate-800 text-white px-4 py-2 text-sm hover:bg-slate-700">
                Filter
            </button>
            <a href="{{ route('transactions.index') }}"
               class="rounded border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                Reset
            </a>
        </div>
    </form>

    <div class="bg-white rounded border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3 font-medium">Tanggal</th>
                    <th class="px-4 py-3 font-medium">Barang</th>
                    <th class="px-4 py-3 font-medium">Tipe</th>
                    <th class="px-4 py-3 font-medium">Jumlah</th>
                    <th class="px-4 py-3 font-medium">Supplier</th>
                    <th class="px-4 py-3 font-medium">Catatan</th>
                    <th class="px-4 py-3 font-medium">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($transactions as $transaction)
                    <tr>
                        <td class="px-4 py-3">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $transaction->item->name }}</td>
                        <td class="px-4 py-3">
                            @if ($transaction->type === 'masuk')
                                <span class="rounded bg-green-50 border border-green-200 px-1.5 py-0.5 text-xs text-green-800">Masuk</span>
                            @else
                                <span class="rounded bg-red-50 border border-red-200 px-1.5 py-0.5 text-xs text-red-800">Keluar</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $transaction->qty }}</td>
                        <td class="px-4 py-3">{{ $transaction->supplier?->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $transaction->note ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $transaction->user->name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
@endsection
