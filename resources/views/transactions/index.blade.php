@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <header class="page-header">
        <div>
            <h1 class="page-title">Riwayat Transaksi</h1>
            @if (! auth()->user()->isAdmin())
                <p class="page-meta">Anda hanya melihat transaksi yang Anda catat sendiri.</p>
            @else
                <p class="page-meta">Semua transaksi stok</p>
            @endif
        </div>
        <a href="{{ route('transactions.create') }}" class="btn btn-primary">Catat Transaksi</a>
    </header>

    <form method="GET" action="{{ route('transactions.index') }}" class="filter-bar">
        <div class="field">
            <label for="type" class="label">Tipe</label>
            <select id="type" name="type" class="input">
                <option value="">Semua</option>
                <option value="masuk" @selected(request('type') === 'masuk')>Masuk</option>
                <option value="keluar" @selected(request('type') === 'keluar')>Keluar</option>
            </select>
        </div>
        <div class="field">
            <label for="start_date" class="label">Dari Tanggal</label>
            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="input">
        </div>
        <div class="field">
            <label for="end_date" class="label">Sampai Tanggal</label>
            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="input">
        </div>
        <div class="actions">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <section class="kartu-label">
        <div class="kartu-label-header">
            <span>Riwayat Transaksi</span>
            <span>{{ $transactions->count() }} baris</span>
        </div>
        <div class="kartu-label-body-flush">
            <div class="table-wrap table-wrap-flush">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Barang</th>
                            <th>Tipe</th>
                            <th class="num">Jumlah</th>
                            <th>Supplier</th>
                            <th>Catatan</th>
                            <th>Dicatat Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td class="mono">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                <td><strong>{{ $transaction->item->name }}</strong></td>
                                <td>
                                    <span class="stempel stempel-{{ $transaction->type }}">
                                        {{ $transaction->type }}
                                    </span>
                                </td>
                                <td class="num"><span class="mono">{{ $transaction->qty }}</span></td>
                                <td class="muted">{{ $transaction->supplier?->name ?? '—' }}</td>
                                <td class="muted">{{ $transaction->note ?? '—' }}</td>
                                <td class="muted">{{ $transaction->user->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state text-center">
                                    @if (request()->filled('type') || request()->filled('start_date') || request()->filled('end_date'))
                                        Tidak ada transaksi yang cocok dengan filter ini.
                                    @else
                                        Belum ada transaksi. Catat transaksi pertama lewat tombol di kanan atas.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="pagination">
        {{ $transactions->links() }}
    </div>
@endsection
