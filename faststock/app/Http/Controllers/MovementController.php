<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function index(Request $request)
    {
        return view('movements.index', [
            'movements' => $this->filtered($request)->with(['ingredient', 'supplier', 'user'])->latest()->paginate(30)->withQueryString(),
            'ingredients' => Ingredient::orderBy('name')->get(),
            'filters' => $request->only('ingredient_id', 'type', 'from', 'to'),
        ]);
    }

    // Physical count correction: user enters what's actually on the shelf.
    public function adjust(Request $request)
    {
        $data = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'counted' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:200',
        ]);

        $ingredient = Ingredient::findOrFail($data['ingredient_id']);
        $delta = $data['counted'] - $ingredient->stock;
        if ($delta == 0.0) {
            return back()->with('ok', 'Count matches current stock, nothing to adjust.');
        }

        StockMovement::record($ingredient, 'adjustment', $delta, ['note' => $data['note'] ?: 'Physical count']);

        return back()->with('ok', "Adjusted $ingredient->name by $delta $ingredient->unit.");
    }

    public static function filtered(Request $request)
    {
        return StockMovement::query()
            ->when($request->ingredient_id, fn ($q, $id) => $q->where('ingredient_id', $id))
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
            ->when($request->from, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($request->to, fn ($q, $to) => $q->whereDate('created_at', '<=', $to));
    }
}
