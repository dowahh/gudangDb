@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')
    <header class="page-header">
        <div>
            <h1 class="page-title">Laporan Stok</h1>
            <p class="page-meta">Rekap seluruh barang dan total nilai gudang saat ini</p>
        </div>
        <div class="page-header-aside">
            <span class="page-date">{{ now()->format('d/m/Y') }}</span>
        </div>
    </header>

    {{-- ringkasan angka: total barang, nilai gudang, jumlah menipis --}}
    <section class="kartu-label section-block">
        <div class="kartu-label-header">
            <span>Ringkasan Stok</span>
            <span>Semua Barang</span>
        </div>
        <div class="angka-row">
            <div class="angka-item">
                <span class="angka-label">Total Jenis Barang</span>
                <span class="angka-besar">{{ $items->count() }}</span>
            </div>
            <div class="angka-item">
                <span class="angka-label">Nilai Gudang</span>
                <span class="angka-besar is-currency">Rp&nbsp;{{ number_format($totalValue, 0, ',', '.') }}</span>
            </div>
            <div class="angka-item">
                <span class="angka-label">Jumlah Menipis</span>
                <span class="angka-besar {{ $lowCount > 0 ? 'is-alert' : '' }}">{{ $lowCount }}</span>
            </div>
        </div>
    </section>

    <div class="pita-hazard pita-hazard-slim section-block" aria-hidden="true"></div>

    <section class="kartu-label">
        <div class="kartu-label-header">
            <span>Daftar Barang</span>
            <span>{{ $items->count() }} baris</span>
        </div>
        <div class="kartu-label-body-flush">
            <div class="table-wrap table-wrap-flush">
                <table class="table">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th class="num">Stok</th>
                            <th class="num">Harga</th>
                            <th class="num">Nilai Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr class="{{ $item->isLowStock() ? 'is-low' : '' }}">
                                <td class="mono">{{ $item->sku }}</td>
                                <td><strong>{{ $item->name }}</strong></td>
                                <td class="muted">{{ $item->category->name }}</td>
                                <td class="num">
                                    <span class="mono">{{ $item->stock }}</span>
                                    @if ($item->isLowStock())
                                        <span class="stempel stempel-menipis">Menipis</span>
                                    @endif
                                </td>
                                <td class="num">Rp&nbsp;<span class="mono">{{ number_format($item->price, 0, ',', '.') }}</span></td>
                                <td class="num">Rp&nbsp;<span class="mono">{{ number_format($item->stock * $item->price, 0, ',', '.') }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state text-center">
                                    Belum ada barang di katalog. Tambah barang dulu lewat menu Barang.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="num">Total Nilai Gudang</td>
                            <td class="num">Rp&nbsp;<span class="mono">{{ number_format($totalValue, 0, ',', '.') }}</span></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
@endsection
