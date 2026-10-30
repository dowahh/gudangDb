<?php

use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\Supplier;
use App\Models\User;

test('staff bisa catat transaksi masuk dan stok bertambah', function () {
    $staff = User::factory()->create();
    $item = Item::factory()->create(['stock' => 10]);
    $supplier = Supplier::factory()->create();

    $response = $this->actingAs($staff)->post(route('transactions.store'), [
        'item_id' => $item->id,
        'type' => 'masuk',
        'qty' => 5,
        'supplier_id' => $supplier->id,
        'note' => 'Pembelian dari supplier',
        'transaction_date' => now()->toDateString(),
    ]);

    $response->assertRedirect(route('transactions.index'))->assertSessionHas('status');

    expect($item->fresh()->stock)->toBe(15);

    $this->assertDatabaseHas('stock_transactions', [
        'item_id' => $item->id,
        'user_id' => $staff->id,
        'supplier_id' => $supplier->id,
        'type' => 'masuk',
        'qty' => 5,
    ]);
});

test('transaksi keluar mengurangi stok dan supplier diabaikan', function () {
    $staff = User::factory()->create();
    $item = Item::factory()->create(['stock' => 10]);
    $supplier = Supplier::factory()->create();

    $this->actingAs($staff)->post(route('transactions.store'), [
        'item_id' => $item->id,
        'type' => 'keluar',
        'qty' => 3,
        // supplier dikirim tapi harus diabaikan untuk keluar
        'supplier_id' => $supplier->id,
        'note' => null,
        'transaction_date' => now()->toDateString(),
    ])->assertRedirect(route('transactions.index'));

    expect($item->fresh()->stock)->toBe(7);

    $transaction = StockTransaction::query()->where('item_id', $item->id)->first();
    expect($transaction->type)->toBe('keluar');
    expect($transaction->supplier_id)->toBeNull();
});

test('transaksi keluar melebihi stok ditolak dengan pesan jelas', function () {
    $staff = User::factory()->create();
    $item = Item::factory()->create(['stock' => 2, 'name' => 'Kardus Uji']);

    $response = $this->actingAs($staff)
        ->from(route('transactions.create'))
        ->post(route('transactions.store'), [
            'item_id' => $item->id,
            'type' => 'keluar',
            'qty' => 5,
            'supplier_id' => null,
            'note' => null,
            'transaction_date' => now()->toDateString(),
        ]);

    $response->assertRedirect(route('transactions.create'));
    $response->assertSessionHasErrors('qty');
    expect(session('errors')->first())->toContain('tidak mencukupi');
    expect($item->fresh()->stock)->toBe(2);
    $this->assertDatabaseCount('stock_transactions', 0);
});

test('staff hanya melihat riwayat miliknya admin melihat semua', function () {
    $staff = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $item = Item::factory()->create();

    $staffTransaction = StockTransaction::factory()->create([
        'item_id' => $item->id,
        'user_id' => $staff->id,
        'note' => 'Catatan staf saja',
    ]);
    StockTransaction::factory()->create([
        'item_id' => $item->id,
        'user_id' => $admin->id,
        'note' => 'Catatan admin saja',
    ]);

    $this->actingAs($staff)->get(route('transactions.index'))
        ->assertOk()
        ->assertSee('Catatan staf saja')
        ->assertDontSee('Catatan admin saja');

    $this->actingAs($admin)->get(route('transactions.index'))
        ->assertOk()
        ->assertSee('Catatan staf saja')
        ->assertSee('Catatan admin saja');

    expect($staffTransaction->user_id)->toBe($staff->id);
});

test('filter tipe dan tanggal di riwayat transaksi', function () {
    $admin = User::factory()->admin()->create();
    $item = Item::factory()->create();

    StockTransaction::factory()->masuk()->create([
        'item_id' => $item->id,
        'user_id' => $admin->id,
        'note' => 'Pembelian Oktober awal',
        'transaction_date' => '2026-10-01',
    ]);
    StockTransaction::factory()->keluar()->create([
        'item_id' => $item->id,
        'user_id' => $admin->id,
        'note' => 'Pengeluaran Oktober tengah',
        'transaction_date' => '2026-10-15',
    ]);

    // filter tipe=masuk: hanya catatan masuk yang muncul di tabel
    $this->actingAs($admin)->get(route('transactions.index', ['type' => 'masuk']))
        ->assertOk()
        ->assertSee('Pembelian Oktober awal')
        ->assertDontSee('Pengeluaran Oktober tengah');

    // filter rentang tanggal: hanya keluar (15 Okt) yang masuk rentang
    $this->actingAs($admin)->get(route('transactions.index', [
        'start_date' => '2026-10-10',
        'end_date' => '2026-10-20',
    ]))
        ->assertOk()
        ->assertSee('Pengeluaran Oktober tengah')
        ->assertDontSee('Pembelian Oktober awal');
});
