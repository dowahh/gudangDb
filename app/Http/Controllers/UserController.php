<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->withCount('stockTransactions')
            ->orderBy('name')
            ->paginate(10);

        return view('users.index', [
            'users' => $users,
        ]);
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        // KEAMANAN: admin tidak boleh mengubah role dirinya sendiri,
        // supaya admin tidak tidak sengaja menjatuhkan dirinya jadi staff
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak bisa mengubah role akun sendiri.');
        }

        $user->update($request->validated());

        return redirect()->route('users.index')
            ->with('status', "Role {$user->name} berhasil diubah menjadi {$user->role}.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        // KEAMANAN: admin tidak boleh menghapus akunnya sendiri
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        // cek relasi dulu supaya tidak error 500 kena foreign key
        if ($user->stockTransactions()->exists()) {
            return back()->with('error', 'User tidak bisa dihapus karena masih punya riwayat transaksi stok.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('status', "User {$user->name} berhasil dihapus.");
    }
}
