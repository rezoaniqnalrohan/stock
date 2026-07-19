<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        return view('suppliers.index', [
            'suppliers' => Supplier::withCount('purchaseOrders')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Supplier::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]));

        return back()->with('status', 'Supplier added.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return back()->with('status', 'Supplier removed.');
    }
}
