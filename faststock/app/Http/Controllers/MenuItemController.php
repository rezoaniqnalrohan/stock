<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MenuItemController extends Controller
{
    public function index()
    {
        return view('menu-items.index', ['items' => MenuItem::with('ingredients')->orderBy('name')->get()]);
    }

    public function create()
    {
        return $this->form(new MenuItem());
    }

    public function store(Request $request)
    {
        $item = MenuItem::create($this->validated($request));
        $this->syncRecipe($item, $request);

        return redirect()->route('menu-items.index')->with('ok', 'Menu item added.');
    }

    public function edit(MenuItem $menu_item)
    {
        return $this->form($menu_item);
    }

    public function update(Request $request, MenuItem $menu_item)
    {
        $menu_item->update($this->validated($request));
        $this->syncRecipe($menu_item, $request);

        return redirect()->route('menu-items.index')->with('ok', 'Menu item updated.');
    }

    public function destroy(MenuItem $menu_item)
    {
        Gate::authorize('admin');
        $menu_item->delete();

        return back()->with('ok', 'Menu item deleted.');
    }

    private function form(MenuItem $item)
    {
        return view('menu-items.form', [
            'item' => $item->load('ingredients'),
            'ingredients' => Ingredient::orderBy('name')->get(),
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
        ]);
    }

    private function syncRecipe(MenuItem $item, Request $request): void
    {
        $rows = $request->validate([
            'recipe' => 'array',
            'recipe.*.ingredient_id' => 'nullable|exists:ingredients,id',
            'recipe.*.qty' => 'nullable|numeric|min:0',
        ])['recipe'] ?? [];

        $item->ingredients()->sync(
            collect($rows)
                ->filter(fn ($r) => ! empty($r['ingredient_id']) && (float) ($r['qty'] ?? 0) > 0)
                ->mapWithKeys(fn ($r) => [$r['ingredient_id'] => ['qty' => $r['qty']]])
        );
    }
}
