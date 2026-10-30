<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

test('login berhasil dan diarahkan ke dashboard', function () {
    $user = User::factory()->create([
        'email' => 'staff@gudang.test',
        'password' => 'password123',
    ]);

    $response = $this->post('/login', [
        'email' => 'staff@gudang.test',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('login gagal menampilkan pesan error umum', function () {
    User::factory()->create([
        'email' => 'staff@gudang.test',
        'password' => 'password123',
    ]);

    $response = $this->from('/login')->post('/login', [
        'email' => 'staff@gudang.test',
        'password' => 'salah',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('email');
    // pesan tidak boleh membocorkan apakah email terdaftar
    expect(session('errors')->first())->toBe('Email atau password salah.');
    $this->assertGuest();
});

test('staff diblokir dari rute admin dengan 403', function () {
    $staff = User::factory()->create(); // role default staff

    $response = $this->actingAs($staff)->get('/categories');

    $response->assertForbidden();
});

test('admin bisa membuka rute admin', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/categories');

    $response->assertOk();
});

test('guest diarahkan ke halaman login', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect(route('login'));
});

test('register membuat user dengan role staff', function () {
    $response = $this->post('/register', [
        'name' => 'Pengguna Baru',
        'email' => 'baru@gudang.test',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();

    $user = User::query()->where('email', 'baru@gudang.test')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('staff');
});

test('login salah berulang kena rate limit', function () {
    User::factory()->create([
        'email' => 'limit@gudang.test',
        'password' => 'password123',
    ]);

    // bersihkan rate limiter supaya tidak tercampur test lain
    $key = Str::lower('limit@gudang.test').'|'.request()->ip();
    RateLimiter::clear($key);

    foreach (range(1, 5) as $i) {
        $this->post('/login', [
            'email' => 'limit@gudang.test',
            'password' => 'salah',
        ]);
    }

    $response = $this->from('/login')->post('/login', [
        'email' => 'limit@gudang.test',
        'password' => 'salah',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('email');
    expect(session('errors')->first())->toContain('Terlalu banyak percobaan login');
    $this->assertGuest();
});
