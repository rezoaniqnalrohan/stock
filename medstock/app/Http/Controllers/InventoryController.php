<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $warehouses = Warehouse::orderBy('name')->get();

        $products = Product::with(['batches' => fn ($q) => $q->with('warehouse')])
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%$s%")->orWhere('sku', 'like', "%$s%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $productOptions = Product::orderBy('name')->pluck('name', 'id');

        return view('inventory.index', compact('products', 'warehouses', 'productOptions'));
    }

    public function adjust(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'quantity' => ['required', 'integer'],
            'note' => ['nullable', 'string'],
        ]);

        DB::transaction(fn () => Batch::move(
            $data['product_id'], $data['warehouse_id'], $data['quantity'], 'adjust', 'Adjustment', $data['note'] ?? null
        ));

        return back()->with('status', 'Stock adjusted.');
    }

    public function transfer(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'from_warehouse_id' => ['required', 'exists:warehouses,id'],
            'to_warehouse_id' => ['required', 'different:from_warehouse_id', 'exists:warehouses,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $available = Batch::where('product_id', $data['product_id'])
            ->where('warehouse_id', $data['from_warehouse_id'])->sum('quantity');

        if ($available < $data['quantity']) {
            return back()->withErrors(['quantity' => "Only $available units available at source warehouse."]);
        }

        DB::transaction(function () use ($data) {
            Batch::move($data['product_id'], $data['from_warehouse_id'], -$data['quantity'], 'transfer', 'Transfer out');
            Batch::move($data['product_id'], $data['to_warehouse_id'], $data['quantity'], 'transfer', 'Transfer in');
        });

        return back()->with('status', 'Stock transferred.');
    }
}
