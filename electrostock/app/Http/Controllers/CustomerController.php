<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customers.index', [
            'customers' => Customer::withCount('sales')->orderByDesc('total_spent')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Customer::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]));

        return back()->with('status', 'Customer added.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return back()->with('status', 'Customer removed.');
    }
}
