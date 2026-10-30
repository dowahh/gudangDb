<?php

use App\Models\User;

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

    $response = $this->actingAs($staff)->get('/admin/contoh');

    $response->assertForbidden();
});

test('admin bisa membuka rute admin', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/admin/contoh');

    $response->assertOk();
});

test('guest diarahkan ke halaman login', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect(route('login'));
});
