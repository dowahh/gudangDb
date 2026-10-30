<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function createRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Simpan user baru. Role selalu 'staff' — tidak boleh dipilih dari form
     * supaya user baru tidak bisa menaikkan dirinya jadi admin.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            // KEAMANAN: password di-hash, tidak pernah disimpan plain text
            'password' => Hash::make($request->string('password')),
            'role' => 'staff',
        ]);

        Auth::login($user);

        // KEAMANAN: session baru setelah register (anti session fixation)
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function createLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        // validasi + attempt + regenerate session ada di LoginRequest::authenticate()
        $request->authenticate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        // KEAMANAN: hapus data session di browser
        $request->session()->invalidate();

        // KEAMANAN: ganti CSRF token supaya token lama tidak bisa dipakai
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
