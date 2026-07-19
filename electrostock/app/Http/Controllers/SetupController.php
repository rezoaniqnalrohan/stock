<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SetupController extends Controller
{
    public function index()
    {
        return view('setup.index', [
            'outlets' => Outlet::withCount('stocks')->orderBy('name')->get(),
            'categories' => Category::withCount('products')->orderBy('name')->get(),
            'brands' => Brand::withCount('products')->orderBy('name')->get(),
            'users' => User::with('outlet')->orderBy('name')->get(),
        ]);
    }

    public function storeOutlet(Request $request)
    {
        Outlet::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:outlets,code'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_warehouse' => ['nullable', 'boolean'],
        ]));

        return back()->with('status', 'Outlet added.');
    }

    public function storeCategory(Request $request)
    {
        Category::create($request->validate(['name' => ['required', 'string', 'max:255']]));

        return back()->with('status', 'Category added.');
    }

    public function storeBrand(Request $request)
    {
        Brand::create($request->validate(['name' => ['required', 'string', 'max:255']]));

        return back()->with('status', 'Brand added.');
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'in:admin,manager,cashier'],
            'outlet_id' => ['nullable', 'exists:outlets,id'],
            'password' => ['required', Password::min(6)],
        ]);
        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return back()->with('status', 'User created.');
    }
}
