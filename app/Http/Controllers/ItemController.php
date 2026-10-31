<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemStoreRequest;
use App\Http\Requests\ItemUpdateRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    /**
     * JSON untuk live search di form transaksi (dipanggil fetch dari JS).
     */
    public function search(Request $request): JsonResponse
    {
        $items = Item::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->string('q');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'sku', 'name', 'stock']);

        return response()->json($items);
    }

    public function index(Request $request): View
    {
        $categories = Category::query()->orderBy('name')->get();

        $items = Item::query()
            ->with('category')
            // pencarian nama atau SKU
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            // filter kategori
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->integer('category_id'));
            })
            ->orderBy('name')
            ->paginate(10)
            // pertahankan parameter pencarian/filter di link pagination
            ->withQueryString();

        return view('items.index', [
            'items' => $items,
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('items.create', [
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function store(ItemStoreRequest $request): RedirectResponse
    {
        // stok tidak diisi dari form — diisi 0, lalu berubah lewat transaksi stok
        $item = Item::create($request->validated());

        return redirect()->route('items.index')
            ->with('status', "Barang {$item->name} berhasil ditambahkan.");
    }

    public function edit(Item $item): View
    {
        return view('items.edit', [
            'item' => $item,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(ItemUpdateRequest $request, Item $item): RedirectResponse
    {
        $item->update($request->validated());

        return redirect()->route('items.index')
            ->with('status', "Barang {$item->name} berhasil diperbarui.");
    }

    public function destroy(Item $item): RedirectResponse
    {
        // cek relasi dulu supaya tidak error 500 kena foreign key
        if ($item->stockTransactions()->exists()) {
            return back()->with('error', "Barang {$item->name} tidak bisa dihapus karena masih punya riwayat transaksi stok.");
        }

        $item->delete();

        return redirect()->route('items.index')
            ->with('status', "Barang {$item->name} berhasil dihapus.");
    }
}
