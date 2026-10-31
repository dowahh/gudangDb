<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierStoreRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::query()
            ->withCount('stockTransactions')
            ->orderBy('name')
            ->paginate(10);

        return view('suppliers.index', [
            'suppliers' => $suppliers,
        ]);
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(SupplierStoreRequest $request): RedirectResponse
    {
        $supplier = Supplier::create($request->validated());

        return redirect()->route('suppliers.index')
            ->with('status', "Supplier {$supplier->name} berhasil ditambahkan.");
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(SupplierUpdateRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return redirect()->route('suppliers.index')
            ->with('status', "Supplier {$supplier->name} berhasil diperbarui.");
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        // cek relasi dulu supaya tidak error 500 kena foreign key
        if ($supplier->stockTransactions()->exists()) {
            return back()->with('error', "Supplier {$supplier->name} tidak bisa dihapus karena masih dipakai oleh transaksi stok.");
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('status', "Supplier {$supplier->name} berhasil dihapus.");
    }
}
