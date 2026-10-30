<?php

use App\Models\StockTransaction;
use App\Models\User;

test('admin bisa mengubah role user lain', function () {
    $admin = User::factory()->admin()->create();
    $staff = User::factory()->create(['role' => 'staff']);

    $this->actingAs($admin)->put(route('users.update', $staff), [
        'role' => 'admin',
    ])->assertRedirect(route('users.index'))->assertSessionHas('status');

    expect($staff->fresh()->role)->toBe('admin');
});

test('admin tidak bisa mengubah role dirinya sendiri', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->put(route('users.update', $admin), [
        'role' => 'staff',
    ]);

    $response->assertSessionHas('error');
    expect(session('error'))->toContain('akun sendiri');
    expect($admin->fresh()->role)->toBe('admin');
});

test('admin tidak bisa menghapus akunnya sendiri', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->delete(route('users.destroy', $admin));

    $response->assertSessionHas('error');
    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

test('user yang punya transaksi tidak bisa dihapus', function () {
    $admin = User::factory()->admin()->create();
    $staff = User::factory()->create();
    StockTransaction::factory()->create(['user_id' => $staff->id]);

    $response = $this->actingAs($admin)->delete(route('users.destroy', $staff));

    $response->assertSessionHas('error');
    expect(session('error'))->toContain('riwayat transaksi');
    $this->assertDatabaseHas('users', ['id' => $staff->id]);
});

test('admin bisa menghapus user tanpa transaksi', function () {
    $admin = User::factory()->admin()->create();
    $staff = User::factory()->create();

    $this->actingAs($admin)->delete(route('users.destroy', $staff))
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('status');

    $this->assertDatabaseMissing('users', ['id' => $staff->id]);
});

test('staff ditolak akses halaman kelola user', function () {
    $staff = User::factory()->create();

    $this->actingAs($staff)->get(route('users.index'))->assertForbidden();
    $this->actingAs($staff)->put(route('users.update', $staff), ['role' => 'admin'])->assertForbidden();
});
