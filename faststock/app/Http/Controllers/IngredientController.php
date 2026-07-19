<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class IngredientController extends Controller
{
    public function index(Request $request)
    {
        $ingredients = Ingredient::when($request->q, fn ($query, $q) => $query->where('name', 'like', "%$q%"))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('ingredients.index', ['ingredients' => $ingredients, 'q' => $request->q]);
    }

    public function create()
    {
        return view('ingredients.form', ['ingredient' => new Ingredient()]);
    }

    public function store(Request $request)
    {
        Ingredient::create($this->validated($request));

        return redirect()->route('ingredients.index')->with('ok', 'Ingredient added.');
    }

    public function edit(Ingredient $ingredient)
    {
        return view('ingredients.form', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $ingredient->update($this->validated($request));

        return redirect()->route('ingredients.index')->with('ok', 'Ingredient updated.');
    }

    public function destroy(Ingredient $ingredient)
    {
        Gate::authorize('admin');
        $ingredient->delete();

        return back()->with('ok', 'Ingredient deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|string|max:50',
            'unit' => 'required|string|max:20',
            'cost' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
        ]);
    }
}
