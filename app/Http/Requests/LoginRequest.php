<?php

namespace App\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ];
    }

    /**
     * Autentikasi user.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'))) {
            // KEAMANAN: pesan sama untuk email salah ATAU password salah,
            // supaya penyerang tidak tahu email terdaftar atau tidak.
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        // KEAMANAN: session lama dibuang, session baru dibuat (anti session fixation)
        session()->regenerate();
    }

    /**
     * Rate limit: maksimal 5 percobaan login per email+IP dalam 1 menit.
     */
    protected function ensureIsNotRateLimited(): void
    {
        $email = (string) $this->input('email');
        $key = Str::lower($email).'|'.$this->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            event(new Lockout($this));

            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        RateLimiter::hit($key, 60);
    }
}
