<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('purchases.index', [
            'purchases' => StockMovement::with(['ingredient', 'supplier', 'user'])
                ->where('type', 'purchase')->latest()->paginate(15),
            'ingredients' => Ingredient::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'qty' => 'required|numeric|gt:0',
            'unit_cost' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        DB::transaction(function () use ($data) {
            $ingredient = Ingredient::findOrFail($data['ingredient_id']);
            StockMovement::record($ingredient, 'purchase', $data['qty'], [
                'supplier_id' => $data['supplier_id'],
                'unit_cost' => $data['unit_cost'],
                'expiry_date' => $data['expiry_date'],
            ]);
            $ingredient->update(['cost' => $data['unit_cost']]); // latest price wins
        });

        return back()->with('ok', 'Purchase recorded, stock updated.');
    }
}
