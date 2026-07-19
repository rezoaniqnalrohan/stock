<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transfer;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function index()
    {
        return view('transfers.index', [
            'transfers' => Transfer::with(['product', 'fromOutlet', 'toOutlet'])->latest()->paginate(15),
            'outlets' => Outlet::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'from_outlet_id' => ['required', 'exists:outlets,id'],
            'to_outlet_id' => ['required', 'different:from_outlet_id', 'exists:outlets,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        // move stock out of the source immediately; it lands on receive
        $source = Stock::firstOrCreate(
            ['product_id' => $data['product_id'], 'outlet_id' => $data['from_outlet_id']],
            ['quantity' => 0]
        );
        if ($source->quantity < $data['quantity']) {
            return back()->withErrors(['quantity' => 'Not enough stock at the source outlet.']);
        }
        $source->decrement('quantity', $data['quantity']);

        Transfer::create($data + ['status' => 'pending']);

        return back()->with('status', 'Transfer created and awaiting receipt.');
    }

    public function receive(Transfer $transfer)
    {
        if ($transfer->status === 'received') {
            return back()->with('status', 'Transfer already received.');
        }

        Stock::firstOrCreate(
            ['product_id' => $transfer->product_id, 'outlet_id' => $transfer->to_outlet_id],
            ['quantity' => 0]
        )->increment('quantity', $transfer->quantity);

        $transfer->update(['status' => 'received', 'received_at' => now()]);

        return back()->with('status', 'Transfer received into destination outlet.');
    }
}
