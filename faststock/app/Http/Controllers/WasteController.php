<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class WasteController extends Controller
{
    public function index()
    {
        return view('waste.index', [
            'waste' => StockMovement::with(['ingredient', 'user'])->where('type', 'waste')->latest()->paginate(15),
            'ingredients' => Ingredient::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'qty' => 'required|numeric|gt:0',
            'note' => 'required|string|max:200',
        ]);

        $ingredient = Ingredient::findOrFail($data['ingredient_id']);
        if ($data['qty'] > $ingredient->stock) {
            return back()->withErrors(['qty' => "Only $ingredient->stock $ingredient->unit in stock."])->withInput();
        }

        StockMovement::record($ingredient, 'waste', -$data['qty'], ['note' => $data['note']]);

        return back()->with('ok', 'Waste recorded.');
    }
}
