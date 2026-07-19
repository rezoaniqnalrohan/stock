<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        return view('purchase-orders.index', [
            'orders' => PurchaseOrder::with(['supplier', 'warehouse'])->latest()->paginate(12),
        ]);
    }

    public function create()
    {
        return view('purchase-orders.form', [
            'suppliers' => Supplier::orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'expected_date' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $po = PurchaseOrder::create([
            'po_number' => 'PO-'.str_pad((string) (PurchaseOrder::max('id') + 1), 4, '0', STR_PAD_LEFT),
            'supplier_id' => $data['supplier_id'],
            'warehouse_id' => $data['warehouse_id'],
            'status' => 'ordered',
            'order_date' => now(),
            'expected_date' => $data['expected_date'] ?? null,
            'total' => 0,
        ]);

        $total = 0;
        foreach ($data['items'] as $item) {
            $po->items()->create($item);
            $total += $item['quantity'] * $item['unit_cost'];
        }
        $po->update(['total' => $total]);

        return redirect('/purchase-orders/'.$po->id)->with('status', 'Purchase order created.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'warehouse', 'items.product']);

        return view('purchase-orders.show', ['order' => $purchaseOrder]);
    }

    // Receiving a PO: create batches + stock-in movements, mark received.
    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return back()->with('status', 'Already received.');
        }

        $purchaseOrder->load('items.product');
        foreach ($purchaseOrder->items as $item) {
            $shelf = $item->product->shelf_life_days;
            Batch::create([
                'product_id' => $item->product_id,
                'warehouse_id' => $purchaseOrder->warehouse_id,
                'batch_no' => $purchaseOrder->po_number.'-'.$item->product->sku,
                'quantity' => $item->quantity,
                'expiry_date' => $shelf > 0 ? now()->addDays($shelf) : null,
            ]);

            StockMovement::create([
                'product_id' => $item->product_id,
                'warehouse_id' => $purchaseOrder->warehouse_id,
                'type' => 'in',
                'quantity' => $item->quantity,
                'reference' => $purchaseOrder->po_number,
                'note' => 'Received against PO',
            ]);
        }

        $purchaseOrder->update(['status' => 'received']);

        return back()->with('status', 'Goods received and stock updated.');
    }
}
