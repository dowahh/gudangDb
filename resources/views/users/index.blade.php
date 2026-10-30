@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Kelola User</h1>
    <p class="mb-4 text-sm text-gray-600">
        User baru mendaftar lewat halaman register (otomatis role staff).
        Di sini admin hanya mengubah role atau menghapus user.
    </p>

    <div class="bg-white rounded border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3 font-medium">Nama</th>
                    <th class="px-4 py-3 font-medium">Email</th>
                    <th class="px-4 py-3 font-medium">Role</th>
                    <th class="px-4 py-3 font-medium">Transaksi</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-4 py-3">
                            {{ $user->name }}
                            @if ($user->is(auth()->user()))
                                <span class="ml-1 text-xs text-gray-400">(Anda)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            @if ($user->is(auth()->user()))
                                {{-- role diri sendiri tidak bisa diubah --}}
                                <span class="text-gray-600">{{ $user->role }}</span>
                            @else
                                <form method="POST" action="{{ route('users.update', $user) }}"
                                      class="inline-flex items-center gap-2">
                                    {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
                                    @csrf
                                    @method('PUT')
                                    <select name="role"
                                            class="rounded border border-gray-300 px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                                        <option value="staff" @selected($user->role === 'staff')>staff</option>
                                        <option value="admin" @selected($user->role === 'admin')>admin</option>
                                    </select>
                                    <button type="submit" class="text-slate-800 underline text-sm">Simpan</button>
                                </form>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $user->stock_transactions_count }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            @if ($user->is(auth()->user()))
                                <span class="text-gray-400 text-sm">—</span>
                            @else
                                <form method="POST" action="{{ route('users.destroy', $user) }}"
                                      class="inline-block" data-confirm="Yakin hapus user '{{ $user->name }}'?">
                                    {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-700 underline">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada user.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
@endsection
