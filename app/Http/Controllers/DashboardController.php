<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $totalItems = Item::query()->count();

        // total nilai seluruh stok: sum(stok × harga)
        $totalValue = (float) Item::query()
            ->selectRaw('COALESCE(SUM(stock * price), 0) as total')
            ->value('total');

        // daftar peringatan stok menipis
        $lowStockItems = Item::query()
            ->with('category')
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('name')
            ->get();

        $todayTransactions = StockTransaction::query()
            ->whereDate('transaction_date', today())
            ->count();

        $recentQuery = StockTransaction::query()
            ->with(['item', 'user'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        // KEAMANAN: staf hanya melihat 5 transaksi terakhir miliknya sendiri;
        // admin melihat 5 transaksi terakhir seluruh gudang
        if (! $request->user()->isAdmin()) {
            $recentQuery->where('user_id', $request->user()->id);
        }

        $recentTransactions = $recentQuery->limit(5)->get();

        // transaksi hari ini (seluruh gudang)
        $todayTransactions = StockTransaction::query()
            ->whereDate('transaction_date', today())
            ->count();

        return view('dashboard', [
            'totalItems' => $totalItems,
            'totalValue' => $totalValue,
            'lowStockItems' => $lowStockItems,
            'recentTransactions' => $recentTransactions,
            'todayTransactions' => $todayTransactions,
        ]);
    }

    /**
     * Data JSON untuk grafik Chart.js: total qty masuk vs keluar 7 hari terakhir.
     */
    public function chart(): JsonResponse
    {
        $labels = [];
        $masuk = [];
        $keluar = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $labels[] = self::formatLabelTanggal(now()->subDays($i));

            $dayTransactions = StockTransaction::query()
                ->whereDate('transaction_date', $date)
                ->get();

            $masuk[] = (int) $dayTransactions->where('type', 'masuk')->sum('qty');
            $keluar[] = (int) $dayTransactions->where('type', 'keluar')->sum('qty');
        }

        return response()->json([
            'labels' => $labels,
            'masuk' => $masuk,
            'keluar' => $keluar,
        ]);
    }

    /**
     * Format label grafik: "30 Okt" (bulan Indonesia tanpa extension).
     */
    private static function formatLabelTanggal($date): string
    {
        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        return $date->format('d').' '.$bulan[(int) $date->format('n') - 1];
    }
}
