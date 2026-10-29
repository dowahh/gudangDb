<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Data barang nyata (bukan faker) supaya kelihatan seperti gudang asli.
     * [nama, sku, kategori index, min_stock, harga]
     */
    private array $items = [
        ['Kardus 40x30', 'PKG-001', 0, 20, 3500],
        ['Kardus 60x40', 'PKG-002', 0, 20, 5500],
        ['Kardus Pindahan 5lapis', 'PKG-003', 0, 10, 12000],
        ['Lakban Bening 48mm', 'PKG-004', 0, 30, 8500],
        ['Lakban Coklat 48mm', 'PKG-005', 0, 30, 7500],
        ['Bubble Wrap 50cm', 'PKG-006', 0, 15, 45000],
        ['Plastik Wrap Palet', 'PKG-007', 0, 10, 65000],
        ['Amplop Coklat F4', 'STA-001', 1, 25, 400],
        ['Pulpen Standard AE7', 'STA-002', 1, 50, 2500],
        ['Kertas A4 70gsm', 'STA-003', 1, 40, 48000],
        ['Spidol Whiteboard Hitam', 'STA-004', 1, 20, 12000],
        ['Map Ordner Biru', 'STA-005', 1, 15, 15500],
        ['Stempel Kantor', 'STA-006', 1, 10, 35000],
        ['Palet Plastik 120x100', 'GUD-001', 2, 8, 285000],
        ['Palet Kayu 120x100', 'GUD-002', 2, 8, 175000],
        ['Rak Siku 5 susun', 'GUD-003', 2, 4, 850000],
        ['Rak Gudang 6 tingkat', 'GUD-004', 2, 3, 2450000],
        ['Hand Pallet 2.5 ton', 'GUD-005', 2, 2, 4850000],
        ['Stiker Label Barcode', 'GUD-006', 2, 25, 18000],
        ['Sarung Tangan Kerja', 'SAF-001', 3, 30, 15000],
        ['Helm Proyek Putih', 'SAF-002', 3, 15, 45000],
        ['Rompi Safety Oranye', 'SAF-003', 3, 15, 55000],
        ['Sepatu Safety Kulit', 'SAF-004', 3, 8, 320000],
        ['Kacamata Safety', 'SAF-005', 3, 12, 65000],
        ['Barcode Scanner Wireless', 'ELK-001', 4, 3, 1250000],
        ['Printer Label Barcode', 'ELK-002', 4, 2, 2850000],
        ['Timbangan Digital 300kg', 'ELK-003', 4, 2, 1850000],
        ['CCTV Dome 4MP', 'ELK-004', 4, 4, 950000],
        ['Lampu LED Panel 40W', 'ELK-005', 4, 10, 185000],
        ['Kulkas Mini Gudang', 'ELK-006', 4, 1, 2250000],
    ];

    private array $categoryNames = [
        'Kemasan',
        'Alat Tulis Kantor',
        'Peralatan Gudang',
        'Keselamatan Kerja',
        'Peralatan Elektronik',
        'Pengamanan',
    ];

    public function run(): void
    {
        $admin = User::create([
            'name' => 'Budi Santoso',
            'email' => 'admin@gudang.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $staffAndi = User::create([
            'name' => 'Andi Wijaya',
            'email' => 'andi@gudang.test',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        $staffSari = User::create([
            'name' => 'Sari Rahayu',
            'email' => 'sari@gudang.test',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        $users = [$admin, $staffAndi, $staffSari];

        $categories = collect($this->categoryNames)->map(function (string $name) {
            return Category::create(['name' => $name]);
        });

        $suppliers = [
            Supplier::create([
                'name' => 'PT Sinar Kemasan Jaya',
                'phone' => '021-5551234',
                'address' => 'Jl. Industri Raya No. 12, Tangerang',
            ]),
            Supplier::create([
                'name' => 'CV Mandiri Alat Tulis',
                'phone' => '021-5552345',
                'address' => 'Jl. Cikini Raya No. 45, Jakarta Pusat',
            ]),
            Supplier::create([
                'name' => 'PT Gudang Perkasa',
                'phone' => '021-5553456',
                'address' => 'Jl. Raya Bekasi KM 18, Jakarta Timur',
            ]),
            Supplier::create([
                'name' => 'UD Safety First',
                'phone' => '031-5554567',
                'address' => 'Jl. Margomulyo No. 8, Surabaya',
            ]),
            Supplier::create([
                'name' => 'PT Elektronik Nusantara',
                'phone' => '022-5555678',
                'address' => 'Jl. Soekarno Hatta No. 230, Bandung',
            ]),
        ];

        // Supplier per kategori index, supaya transaksi masuk masuk akal
        $supplierByCategory = [0, 1, 2, 3, 4, 2];

        $today = now()->startOfDay();

        foreach ($this->items as [$name, $sku, $categoryIndex, $minStock, $price]) {
            $item = Item::create([
                'category_id' => $categories[$categoryIndex]->id,
                'sku' => $sku,
                'name' => $name,
                'stock' => 0,
                'min_stock' => $minStock,
                'price' => $price,
            ]);

            // Stok dihitung dari transaksi, bukan diisi acak:
            // total ~60 transaksi untuk 30 barang (rata-rata 2 per barang).
            $masukCount = random_int(1, 2);
            $keluarCount = random_int(0, 1);
            $stock = 0;
            $dates = $this->randomDates($masukCount + $keluarCount, $today);

            for ($i = 0; $i < $masukCount; $i++) {
                $qty = random_int(5, 40);
                $stock += $qty;

                StockTransaction::create([
                    'item_id' => $item->id,
                    'user_id' => $users[array_rand($users)]->id,
                    'supplier_id' => $suppliers[$supplierByCategory[$categoryIndex]]->id,
                    'type' => 'masuk',
                    'qty' => $qty,
                    'note' => 'Pembelian stok',
                    'transaction_date' => $dates[$i],
                ]);
            }

            for ($i = 0; $i < $keluarCount; $i++) {
                // batasi keluar supaya stok tidak minus
                $max = min(15, $stock);
                if ($max < 1) {
                    break;
                }
                $qty = random_int(1, $max);
                $stock -= $qty;

                StockTransaction::create([
                    'item_id' => $item->id,
                    'user_id' => $users[array_rand($users)]->id,
                    'supplier_id' => null, // keluar tidak pakai supplier
                    'type' => 'keluar',
                    'qty' => $qty,
                    'note' => 'Pengeluaran ke unit',
                    'transaction_date' => $dates[$masukCount + $i],
                ]);
            }

            // simpan stok hasil hitungan transaksi
            $item->update(['stock' => $stock]);
        }
    }

    /**
     * Acak tanggal dalam 30 hari terakhir, urut menaik supaya chronologis.
     */
    private function randomDates(int $count, $today): array
    {
        $dates = [];
        for ($i = 0; $i < $count; $i++) {
            $offset = random_int(0, 29);
            $dates[] = $today->copy()->subDays($offset)->toDateString();
        }
        sort($dates);

        return $dates;
    }
}
