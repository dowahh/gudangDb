<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\User;

test('admin bisa membuat dan mengubah barang', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    $this->actingAs($admin)->post(route('items.store'), [
        'category_id' => $category->id,
        'sku' => 'TEST-001',
        'name' => 'Barang Uji',
        'min_stock' => 5,
        'price' => 10000,
    ])->assertRedirect(route('items.index'))->assertSessionHas('status');

    $item = Item::query()->where('sku', 'TEST-001')->first();
    expect($item)->not->toBeNull();
    expect($item->stock)->toBe(0);

    $this->actingAs($admin)->put(route('items.update', $item), [
        'category_id' => $category->id,
        'sku' => 'TEST-001',
        'name' => 'Barang Uji Diubah',
        'min_stock' => 10,
        'price' => 12000,
    ])->assertRedirect(route('items.index'));

    expect($item->fresh()->name)->toBe('Barang Uji Diubah');
});

test('staff ditolak membuat menyimpan dan menghapus barang', function () {
    $staff = User::factory()->create();
    $item = Item::factory()->create();

    $this->actingAs($staff)->get(route('items.create'))->assertForbidden();
    $this->actingAs($staff)->post(route('items.store'), [])->assertForbidden();
    $this->actingAs($staff)->delete(route('items.destroy', $item))->assertForbidden();
});

test('staff tetap bisa melihat daftar barang', function () {
    $staff = User::factory()->create();
    Item::factory()->count(3)->create();

    $this->actingAs($staff)->get(route('items.index'))->assertOk();
});

test('hapus kategori yang masih punya barang ditolak', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();
    Item::factory()->create(['category_id' => $category->id]);

    $response = $this->actingAs($admin)->delete(route('categories.destroy', $category));

    $response->assertSessionHas('error');
    expect(session('error'))->toContain('masih dipakai oleh barang');
    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});

test('sku barang harus unik', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();
    Item::factory()->create([
        'category_id' => $category->id,
        'sku' => 'DUP-001',
    ]);

    $response = $this->actingAs($admin)->from(route('items.create'))->post(route('items.store'), [
        'category_id' => $category->id,
        'sku' => 'DUP-001',
        'name' => 'Barang Duplikat',
        'min_stock' => 1,
        'price' => 1000,
    ]);

    $response->assertRedirect(route('items.create'));
    $response->assertSessionHasErrors('sku');
    expect(Item::query()->where('sku', 'DUP-001')->count())->toBe(1);
});

test('update barang mengabaikan sku miliknya sendiri', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();
    $item = Item::factory()->create([
        'category_id' => $category->id,
        'sku' => 'OWN-001',
    ]);

    $response = $this->actingAs($admin)->put(route('items.update', $item), [
        'category_id' => $category->id,
        'sku' => 'OWN-001',
        'name' => 'Barang Rename',
        'min_stock' => 2,
        'price' => 5000,
    ]);

    $response->assertRedirect(route('items.index'));
    expect($item->fresh()->name)->toBe('Barang Rename');
});
