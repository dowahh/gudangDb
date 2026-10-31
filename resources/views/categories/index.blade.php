@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
    <header class="page-header">
        <div>
            <h1 class="page-title">Kategori</h1>
            <p class="page-meta">{{ $categories->total() }} kategori terdaftar</p>
        </div>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">Tambah Kategori</a>
    </header>

    <section class="kartu-label">
        <div class="kartu-label-header">
            <span>Daftar Kategori</span>
            <span>{{ $categories->count() }} baris</span>
        </div>
        <div class="kartu-label-body-flush">
            <div class="table-wrap table-wrap-flush">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th class="num">Jumlah Barang</th>
                            <th class="num">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td class="num"><span class="mono">{{ $category->items_count }}</span></td>
                                <td class="num">
                                    <div class="action-cell">
                                        <a href="{{ route('categories.edit', $category) }}"
                                           class="btn btn-secondary btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                              data-confirm="Yakin hapus kategori '{{ $category->name }}'?">
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
                                <td colspan="3" class="empty-state text-center">
                                    Belum ada kategori. Tambah kategori pertama lewat tombol di kanan atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="pagination">
        {{ $categories->links() }}
    </div>
@endsection
