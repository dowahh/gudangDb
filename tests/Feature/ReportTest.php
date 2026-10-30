<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\User;

test('admin bisa buka laporan stok dan melihat total nilai yang benar', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    // barang 1: stok 10 × 5000 = 50.000
    Item::factory()->create([
        'category_id' => $category->id,
        'name' => 'Kardus Besar',
        'sku' => 'RPT-001',
        'stock' => 10,
        'min_stock' => 1,
        'price' => 5000,
    ]);
    // barang 2: stok 1 × 1000 = 1.000 (menipis)
    Item::factory()->create([
        'category_id' => $category->id,
        'name' => 'Lakban Kecil',
        'sku' => 'RPT-002',
        'stock' => 1,
        'min_stock' => 5,
        'price' => 1000,
    ]);

    $response = $this->actingAs($admin)->get(route('reports.stock'));

    $response->assertOk();
    $response->assertSee('Laporan Stok');
    $response->assertSee('Kardus Besar');
    $response->assertSee('Lakban Kecil');
    $response->assertSee('Menipis');
    $response->assertSee(number_format(5000, 0, ',', '.'));
    $response->assertSee(number_format(1000, 0, ',', '.'));
    // total = 50.000 + 1.000 = 51.000
    $response->assertSee(number_format(51000, 0, ',', '.'));
});

test('staff ditolak buka laporan stok', function () {
    $staff = User::factory()->create();

    $this->actingAs($staff)->get(route('reports.stock'))->assertForbidden();
});

test('guest diarahkan ke login dari laporan stok', function () {
    $this->get(route('reports.stock'))->assertRedirect(route('login'));
});
