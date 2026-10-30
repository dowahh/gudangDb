<?php

use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\User;

test('dashboard menampilkan statistik dan bisa dibuka staff', function () {
    $staff = User::factory()->create();
    Item::factory()->create(['stock' => 10, 'price' => 5000, 'min_stock' => 1]);
    Item::factory()->create(['stock' => 1, 'price' => 1000, 'min_stock' => 5]); // menipis

    $response = $this->actingAs($staff)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Total Jenis Barang');
    $response->assertSee('Total Nilai Stok');
    $response->assertSee('Stok Menipis');
    $response->assertSee('transaction-chart');
    // total nilai = (10*5000) + (1*1000) = 51000
    $response->assertSee(number_format(51000, 0, ',', '.'));
});

test('guest tidak bisa buka dashboard', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('endpoint chart mengembalikan 7 hari data json', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();

    StockTransaction::factory()->masuk()->create([
        'item_id' => $item->id,
        'user_id' => $user->id,
        'qty' => 4,
        'transaction_date' => now()->toDateString(),
    ]);
    StockTransaction::factory()->keluar()->create([
        'item_id' => $item->id,
        'user_id' => $user->id,
        'qty' => 2,
        'transaction_date' => now()->toDateString(),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard.chart'));

    $response->assertOk();
    $response->assertJsonCount(7, 'labels');
    $response->assertJsonCount(7, 'masuk');
    $response->assertJsonCount(7, 'keluar');

    $data = $response->json();
    $todayIndex = count($data['labels']) - 1;
    expect($data['masuk'][$todayIndex])->toBe(4);
    expect($data['keluar'][$todayIndex])->toBe(2);
});

test('search barang mengembalikan json nama atau sku', function () {
    $user = User::factory()->create();
    Item::factory()->create(['name' => 'Kardus Besar', 'sku' => 'CARI-001']);
    Item::factory()->create(['name' => 'Lakban Kecil', 'sku' => 'LAIN-002']);

    $this->actingAs($user)->get(route('items.search', ['q' => 'Kardus']))
        ->assertOk()
        ->assertJsonFragment(['name' => 'Kardus Besar'])
        ->assertJsonMissing(['name' => 'Lakban Kecil']);

    $this->actingAs($user)->get(route('items.search', ['q' => 'LAIN-002']))
        ->assertOk()
        ->assertJsonFragment(['sku' => 'LAIN-002']);

    $this->actingAs($user)->get(route('items.search'))
        ->assertOk()
        ->assertJsonCount(2);
});

test('guest tidak bisa akses search barang', function () {
    $this->get(route('items.search'))->assertRedirect(route('login'));
});
