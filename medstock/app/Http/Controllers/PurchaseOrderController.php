<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        return view('purchase-orders.index', [
            'orders' => PurchaseOrder::with('supplier', 'warehouse', 'items')->latest('order_date')->paginate(12),
        ]);
    }

    public function create()
    {
        return view('purchase-orders.create', [
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
            'order_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.cost' => ['required', 'numeric', 'min:0'],
        ]);

        $po = DB::transaction(function () use ($data) {
            $po = PurchaseOrder::create([
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'],
                'order_date' => $data['order_date'],
                'status' => 'ordered',
                'user_id' => Auth::id(),
            ]);
            $total = 0;
            foreach ($data['items'] as $item) {
                $po->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'cost' => $item['cost'],
                    'expiry_date' => now()->addYear(),
                ]);
                $total += $item['quantity'] * $item['cost'];
            }
            $po->update(['total' => $total]);

            return $po;
        });

        return redirect()->route('purchase-orders.show', $po)->with('status', 'Purchase order created.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'warehouse', 'items.product');

        return view('purchase-orders.show', ['order' => $purchaseOrder]);
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return back()->withErrors(['status' => 'This order was already received.']);
        }

        DB::transaction(function () use ($purchaseOrder) {
            foreach ($purchaseOrder->items as $item) {
                Batch::move(
                    $item->product_id, $purchaseOrder->warehouse_id, $item->quantity,
                    'in', 'PO-'.$purchaseOrder->id, 'Received', $item->lot_number ?? 'PO'.$purchaseOrder->id, $item->expiry_date
                );
            }
            $purchaseOrder->update(['status' => 'received']);
        });

        return back()->with('status', 'Stock received into inventory.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')->with('status', 'Purchase order deleted.');
    }
}
