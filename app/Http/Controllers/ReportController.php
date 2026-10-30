<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Laporan stok: rekap semua barang + total nilai gudang. Read-only, tanpa paginate.
     */
    public function stock(): View
    {
        $items = Item::query()
            ->with('category')
            ->orderBy('name')
            ->get();

        // total nilai gudang dari SEMUA barang
        $totalValue = (float) $items->sum(fn (Item $item) => $item->stock * $item->price);

        return view('reports.stock', [
            'items' => $items,
            'totalValue' => $totalValue,
        ]);
    }
}
