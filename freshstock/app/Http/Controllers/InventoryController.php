<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    // Stock levels: product x warehouse matrix from batch quantities.
    public function index(Request $request)
    {
        $warehouses = Warehouse::orderBy('name')->get();

        $products = Product::with(['unit', 'batches'])
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%$s%")->orWhere('sku', 'like', "%$s%"))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('inventory.index', compact('products', 'warehouses'));
    }

    public function expiring()
    {
        $batches = Batch::with(['product', 'warehouse'])
            ->whereNotNull('expiry_date')
            ->where('quantity', '>', 0)
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->orderBy('expiry_date')
            ->paginate(20);

        return view('inventory.expiring', compact('batches'));
    }

    public function adjustForm()
    {
        return view('inventory.adjust', [
            'products' => Product::orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('name')->get(),
        ]);
    }

    public function adjustStore(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'quantity' => ['required', 'integer', 'not_in:0'],
            'note' => ['nullable', 'string'],
        ]);

        $this->applyChange($data['product_id'], $data['warehouse_id'], $data['quantity']);

        StockMovement::create([
            'product_id' => $data['product_id'],
            'warehouse_id' => $data['warehouse_id'],
            'type' => 'adjustment',
            'quantity' => $data['quantity'],
            'reference' => 'ADJ-'.now()->format('ymdHis'),
            'note' => $data['note'] ?? 'Manual adjustment',
        ]);

        return redirect('/inventory')->with('status', 'Stock adjusted.');
    }

    public function transferForm()
    {
        return view('inventory.transfer', [
            'products' => Product::orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('name')->get(),
        ]);
    }

    public function transferStore(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'from_warehouse_id' => ['required', 'exists:warehouses,id'],
            'to_warehouse_id' => ['required', 'different:from_warehouse_id', 'exists:warehouses,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $ref = 'TRF-'.now()->format('ymdHis');
        $this->applyChange($data['product_id'], $data['from_warehouse_id'], -$data['quantity']);
        $this->applyChange($data['product_id'], $data['to_warehouse_id'], $data['quantity']);

        foreach ([[$data['from_warehouse_id'], -$data['quantity']], [$data['to_warehouse_id'], $data['quantity']]] as [$wh, $qty]) {
            StockMovement::create([
                'product_id' => $data['product_id'],
                'warehouse_id' => $wh,
                'type' => 'transfer',
                'quantity' => $qty,
                'reference' => $ref,
                'note' => 'Warehouse transfer',
            ]);
        }

        return redirect('/inventory')->with('status', 'Transfer recorded.');
    }

    // Apply a signed quantity change to a product's batch in a warehouse (FEFO-ish: earliest expiry first).
    private function applyChange(int $productId, int $warehouseId, int $delta): void
    {
        $batch = Batch::where('product_id', $productId)->where('warehouse_id', $warehouseId)
            ->orderByRaw('expiry_date is null, expiry_date')->first();

        if (! $batch) {
            $batch = Batch::create([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'batch_no' => 'B'.now()->format('y').'-'.rand(1000, 9999),
                'quantity' => 0,
                'expiry_date' => null,
            ]);
        }

        $batch->increment('quantity', $delta);
    }
}
