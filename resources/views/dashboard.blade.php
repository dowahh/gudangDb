@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $tanggalHariIni = $hari[now()->dayOfWeek] . ', ' . now()->day . ' ' . $bulan[now()->month - 1] . ' ' . now()->year;

        // format nilai stok ringkas: "Rp 744,2 jt" / "Rp 1,2 mrd", judul hover = angka penuh
        $nilaiPenuh = 'Rp ' . number_format($totalValue, 0, ',', '.');
        if ($totalValue >= 1_000_000_000) {
            $nilaiRingkas = 'Rp ' . number_format($totalValue / 1_000_000_000, 1, ',', '.') . ' mrd';
        } elseif ($totalValue >= 1_000_000) {
            $nilaiRingkas = 'Rp ' . number_format($totalValue / 1_000_000, 1, ',', '.') . ' jt';
        } else {
            $nilaiRingkas = $nilaiPenuh;
        }
    @endphp

    <header class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-meta">{{ auth()->user()->name }} · {{ auth()->user()->role }}</p>
        </div>
        <div class="page-header-aside">
            <span class="page-date">{{ $tanggalHariIni }}</span>
        </div>
    </header>

    {{-- ringkasan angka: satu panel label gudang --}}
    <section class="kartu-label section-block">
        <div class="kartu-label-header">
            <span>Ringkasan Gudang</span>
            <span>Hari Ini</span>
        </div>
        <div class="angka-row">
            <div class="angka-item">
                <span class="angka-label">Total Jenis Barang</span>
                <span class="angka-besar">{{ $totalItems }}</span>
            </div>
            <div class="angka-item">
                <span class="angka-label">Nilai Stok</span>
                <span class="angka-besar is-currency" title="{{ $nilaiPenuh }}">{{ $nilaiRingkas }}</span>
            </div>
            <div class="angka-item">
                <span class="angka-label">Stok Menipis</span>
                <span class="angka-besar {{ $lowStockItems->count() > 0 ? 'is-alert' : '' }}">{{ $lowStockItems->count() }}</span>
            </div>
            <div class="angka-item">
                <span class="angka-label">Transaksi Hari Ini</span>
                <span class="angka-besar">{{ $todayTransactions }}</span>
            </div>
        </div>
    </section>

    <div class="pita-hazard pita-hazard-slim section-block" aria-hidden="true"></div>

    <div class="dashboard-grid">
        {{-- grafik 7 hari --}}
        <section class="kartu-label">
            <div class="kartu-label-header">
                <span>Transaksi 7 Hari</span>
                <span>Masuk / Keluar</span>
            </div>
            <div class="kartu-label-body">
                <div class="chart-legend">
                    <span><i class="swatch-masuk"></i>Masuk</span>
                    <span><i class="swatch-keluar"></i>Keluar</span>
                </div>
                <div class="chart-frame">
                    {{-- data-url dibaca JS untuk fetch data chart dari endpoint JSON --}}
                    <canvas id="transaction-chart"
                            data-url="{{ route('dashboard.chart') }}"></canvas>
                </div>
            </div>
        </section>

        {{-- stok menipis --}}
        <section class="kartu-label">
            <div class="kartu-label-header">
                <span>
                    <span class="stempel stempel-menipis">Menipis</span>
                    Stok Menipis — Segera Order
                </span>
                <span>{{ $lowStockItems->count() }} item</span>
            </div>
            @if ($lowStockItems->isEmpty())
                <p class="empty-state">Semua stok aman. Tidak ada yang perlu dipesan.</p>
            @else
                <ul class="list-plain">
                    @foreach ($lowStockItems as $item)
                        <li class="is-low">
                            <span>
                                <span class="item-name">{{ $item->name }}</span>
                                <span class="item-sku">{{ $item->sku }}</span>
                            </span>
                            <span class="qty-low">sisa <strong>{{ $item->stock }}</strong> / min {{ $item->min_stock }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    {{-- transaksi terakhir --}}
    <section class="kartu-label">
        <div class="kartu-label-header">
            <span>Transaksi Terakhir</span>
            <span>5 Baris</span>
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
                            <th>Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTransactions as $transaction)
                            <tr>
                                <td class="mono">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                <td>{{ $transaction->item->name }}</td>
                                <td>
                                    <span class="stempel stempel-{{ $transaction->type }}">
                                        {{ $transaction->type }}
                                    </span>
                                </td>
                                <td class="num mono">{{ $transaction->qty }}</td>
                                <td class="muted">{{ $transaction->user->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 muted">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
