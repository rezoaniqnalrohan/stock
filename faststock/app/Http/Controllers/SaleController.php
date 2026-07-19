<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index()
    {
        return view('sales.index', [
            'sales' => Sale::with(['items.menuItem', 'user'])->latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return view('sales.create', ['items' => MenuItem::with('ingredients')->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $items = collect($request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'integer|min:0',
        ])['items'])->filter()->map(fn ($qty) => (int) $qty);

        throw_if($items->isEmpty(), ValidationException::withMessages(['items' => 'Add at least one item to the order.']));

        $sale = DB::transaction(function () use ($items) {
            $menuItems = MenuItem::with('ingredients')->findOrFail($items->keys());

            // Total ingredient demand across the whole order.
            $needed = [];
            foreach ($menuItems as $menuItem) {
                foreach ($menuItem->ingredients as $ingredient) {
                    $needed[$ingredient->id] = ($needed[$ingredient->id] ?? 0) + $ingredient->pivot->qty * $items[$menuItem->id];
                }
            }

            $short = Ingredient::findOrFail(array_keys($needed))
                ->filter(fn ($i) => $i->stock < $needed[$i->id])
                ->map(fn ($i) => "$i->name (have $i->stock $i->unit, need {$needed[$i->id]})");
            throw_if($short->isNotEmpty(), ValidationException::withMessages(['items' => 'Insufficient stock: '.$short->join(', ')]));

            $sale = Sale::create([
                'user_id' => auth()->id(),
                'total' => $menuItems->sum(fn ($m) => $m->price * $items[$m->id]),
            ]);
            foreach ($menuItems as $menuItem) {
                $sale->items()->create(['menu_item_id' => $menuItem->id, 'qty' => $items[$menuItem->id], 'price' => $menuItem->price]);
            }
            foreach ($needed as $ingredientId => $qty) {
                StockMovement::record(Ingredient::findOrFail($ingredientId), 'sale', -$qty, ['note' => "Sale #$sale->id"]);
            }

            return $sale;
        });

        return redirect()->route('sales.create')->with('ok', "Sale #$sale->id recorded — ৳".number_format((float) $sale->total));
    }
}
