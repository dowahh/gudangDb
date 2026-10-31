@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
    <header class="page-header">
        <div>
            <h1 class="page-title">Kelola User</h1>
            <p class="page-meta">{{ $users->total() }} akun terdaftar</p>
        </div>
        <div class="page-header-aside">
            <span class="page-date">{{ now()->format('d/m/Y') }}</span>
        </div>
    </header>

    <p class="page-note">
        User baru mendaftar lewat halaman register (otomatis role staff).
        Di sini admin hanya mengubah role atau menghapus user.
    </p>

    <section class="kartu-label">
        <div class="kartu-label-header">
            <span>Daftar User</span>
            <span>{{ $users->count() }} baris</span>
        </div>
        <div class="kartu-label-body-flush">
            <div class="table-wrap table-wrap-flush">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="num">Transaksi</th>
                            <th class="num">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                    @if ($user->is(auth()->user()))
                                        <span class="sku-inline">(Anda)</span>
                                    @endif
                                </td>
                                <td class="muted">{{ $user->email }}</td>
                                <td>
                                    {{-- stempel role: admin garis tebal, staff outline tipis --}}
                                    <span class="stempel {{ $user->role === 'admin' ? 'stempel-aman' : 'stempel-tipis' }}">
                                        {{ $user->role }}
                                    </span>
                                    @unless ($user->is(auth()->user()))
                                        {{-- role diri sendiri tidak bisa diubah --}}
                                        <form method="POST" action="{{ route('users.update', $user) }}"
                                              class="role-form">
                                            {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
                                            @csrf
                                            @method('PUT')
                                            <select name="role" aria-label="Role {{ $user->name }}"
                                                    class="input input-sm">
                                                <option value="staff" @selected($user->role === 'staff')>staff</option>
                                                <option value="admin" @selected($user->role === 'admin')>admin</option>
                                            </select>
                                            <button type="submit" class="btn btn-secondary btn-sm">Simpan</button>
                                        </form>
                                    @endunless
                                </td>
                                <td class="num"><span class="mono">{{ $user->stock_transactions_count }}</span></td>
                                <td class="num">
                                    @if ($user->is(auth()->user()))
                                        <span class="muted">—</span>
                                    @else
                                        <div class="action-cell">
                                            <form method="POST" action="{{ route('users.destroy', $user) }}"
                                                  data-confirm="Yakin hapus user '{{ $user->name }}'?">
                                                {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state text-center">
                                    Belum ada user lain selain akun Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="pagination">
        {{ $users->links() }}
    </div>
@endsection
