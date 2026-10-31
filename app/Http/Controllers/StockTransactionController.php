<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionStoreRequest;
use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $query = StockTransaction::query()
            ->with(['item', 'user', 'supplier'])
            ->when($request->filled('type'), function ($q) use ($request) {
                $q->where('type', $request->string('type'));
            })
            ->when($request->filled('start_date'), function ($q) use ($request) {
                $q->whereDate('transaction_date', '>=', $request->date('start_date'));
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                $q->whereDate('transaction_date', '<=', $request->date('end_date'));
            });

        // KEAMANAN: staf hanya melihat riwayat miliknya sendiri; admin melihat semua
        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $transactions = $query
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('transactions.index', [
            'transactions' => $transactions,
        ]);
    }

    public function create(): View
    {
        return view('transactions.create', [
            'suppliers' => Supplier::query()->orderBy('name')->get(),
        ]);
    }

    public function store(TransactionStoreRequest $request): RedirectResponse
    {
        $type = $request->string('type')->toString();
        $qty = $request->integer('qty');

        $itemName = DB::transaction(function () use ($request, $type, $qty) {
            // KEAMANAN: kunci baris barang supaya dua transaksi bersamaan
            // tidak membuat stok salah hitung (race condition)
            $item = Item::query()->lockForUpdate()->findOrFail($request->integer('item_id'));

            if ($type === 'keluar' && $qty > $item->stock) {
                throw ValidationException::withMessages([
                    'qty' => "Stok tidak mencukupi. Sisa {$item->stock}, kamu minta {$qty}.",
                ]);
            }

            StockTransaction::create([
                'item_id' => $item->id,
                // KEAMANAN: user_id selalu dari akun yang login, bukan dari form
                'user_id' => $request->user()->id,
                // keluar tidak pakai supplier
                'supplier_id' => $type === 'keluar' ? null : $request->input('supplier_id'),
                'type' => $type,
                'qty' => $qty,
                'note' => $request->input('note'),
                'transaction_date' => $request->date('transaction_date'),
            ]);

            $item->stock = $type === 'masuk'
                ? $item->stock + $qty
                : $item->stock - $qty;
            $item->save();

            return $item->name;
        });

        return redirect()->route('transactions.index')
            ->with('status', "Transaksi {$itemName} ({$type}) berhasil dicatat.");
    }
}
