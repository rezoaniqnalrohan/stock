<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// ponytail: settings entities get add + delete only (no edit views). Add edit forms if in-place renaming is needed.
class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index', [
            'warehouses' => Warehouse::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function storeWarehouse(Request $request)
    {
        Warehouse::create($request->validate([
            'name' => ['required', 'string'],
            'code' => ['required', 'string', 'unique:warehouses,code'],
            'location' => ['nullable', 'string'],
            'is_cold_chain' => ['nullable', 'boolean'],
        ]) + ['is_cold_chain' => $request->boolean('is_cold_chain')]);

        return back()->with('status', 'Warehouse added.');
    }

    public function storeCategory(Request $request)
    {
        Category::create($request->validate([
            'name' => ['required', 'string'],
            'color' => ['required', 'string'],
        ]));

        return back()->with('status', 'Category added.');
    }

    public function storeUnit(Request $request)
    {
        Unit::create($request->validate([
            'name' => ['required', 'string'],
            'abbreviation' => ['required', 'string', 'max:12'],
        ]));

        return back()->with('status', 'Unit added.');
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'in:admin,warehouse_manager,procurement_officer'],
            'password' => ['required', 'string', 'min:6'],
        ]);
        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return back()->with('status', 'User added.');
    }

    public function destroy(string $type, int $id)
    {
        $model = match ($type) {
            'warehouse' => Warehouse::class,
            'category' => Category::class,
            'unit' => Unit::class,
            'user' => User::class,
            default => abort(404),
        };

        // Don't let an admin delete themselves.
        if ($type === 'user' && (int) auth()->id() === $id) {
            return back()->with('status', 'You cannot delete your own account.');
        }

        $model::findOrFail($id)->delete();

        return back()->with('status', ucfirst($type).' deleted.');
    }
}
