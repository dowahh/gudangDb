@extends('layouts.app')

@section('title', 'Supplier')

@section('content')
    <header class="page-header">
        <div>
            <h1 class="page-title">Supplier</h1>
            <p class="page-meta">{{ $suppliers->total() }} pemasok terdaftar</p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">Tambah Supplier</a>
    </header>

    <section class="kartu-label">
        <div class="kartu-label-header">
            <span>Daftar Supplier</span>
            <span>{{ $suppliers->count() }} baris</span>
        </div>
        <div class="kartu-label-body-flush">
            <div class="table-wrap table-wrap-flush">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th class="num">Transaksi</th>
                            <th class="num">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td><strong>{{ $supplier->name }}</strong></td>
                                <td class="mono">{{ $supplier->phone ?? '—' }}</td>
                                <td class="muted">{{ $supplier->address ?? '—' }}</td>
                                <td class="num"><span class="mono">{{ $supplier->stock_transactions_count }}</span></td>
                                <td class="num">
                                    <div class="action-cell">
                                        <a href="{{ route('suppliers.edit', $supplier) }}"
                                           class="btn btn-secondary btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}"
                                              data-confirm="Yakin hapus supplier '{{ $supplier->name }}'?">
                                            {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state text-center">
                                    Belum ada supplier. Tambah supplier pertama lewat tombol di kanan atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="pagination">
        {{ $suppliers->links() }}
    </div>
@endsection
