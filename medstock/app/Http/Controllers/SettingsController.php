<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    // Map of the four simple settings collections this screen manages.
    private const TYPES = [
        'warehouses' => Warehouse::class,
        'categories' => Category::class,
        'units' => Unit::class,
        'users' => User::class,
    ];

    public function index()
    {
        return view('settings.index', [
            'warehouses' => Warehouse::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, string $type)
    {
        abort_unless(isset(self::TYPES[$type]), 404);

        $data = match ($type) {
            'warehouses' => $request->validate(['name' => 'required|string', 'location' => 'nullable|string']),
            'categories' => $request->validate(['name' => 'required|string']),
            'units' => $request->validate(['name' => 'required|string', 'abbreviation' => 'required|string|max:12']),
            'users' => $this->userData($request),
        };

        (self::TYPES[$type])::create($data);

        return back()->with('status', ucfirst(rtrim($type, 's')).' added.');
    }

    public function destroy(string $type, int $id)
    {
        abort_unless(isset(self::TYPES[$type]), 404);
        (self::TYPES[$type])::findOrFail($id)->delete();

        return back()->with('status', 'Removed.');
    }

    private function userData(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'role' => 'required|in:admin,manager,sales',
            'password' => 'required|string|min:6',
        ]);
        $data['password'] = Hash::make($data['password']);

        return $data;
    }
}
