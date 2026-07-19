<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Stock;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        return view('purchase_orders.index', [
            'orders' => PurchaseOrder::with(['supplier', 'outlet', 'items.product'])->latest()->paginate(12),
        ]);
    }

    public function create()
    {
        return view('purchase_orders.create', [
            'suppliers' => Supplier::orderBy('name')->get(),
            'outlets' => Outlet::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'outlet_id' => ['required', 'exists:outlets,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.cost' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($data) {
            $po = PurchaseOrder::create([
                'reference' => 'PO-' . str_pad((int) PurchaseOrder::max('id') + 1, 4, '0', STR_PAD_LEFT),
                'supplier_id' => $data['supplier_id'],
                'outlet_id' => $data['outlet_id'],
                'status' => 'draft',
            ]);
            $total = 0;
            foreach ($data['items'] as $line) {
                PurchaseOrderItem::create(['purchase_order_id' => $po->id] + $line);
                $total += $line['quantity'] * $line['cost'];
            }
            $po->update(['total' => $total]);
        });

        return redirect()->route('purchase-orders.index')->with('status', 'Purchase order created.');
    }

    public function dispatchOrder(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'draft') {
            $purchaseOrder->update(['status' => 'dispatched']);
        }

        return back()->with('status', "{$purchaseOrder->reference} marked as dispatched.");
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'received') {
            DB::transaction(function () use ($purchaseOrder) {
                foreach ($purchaseOrder->items as $item) {
                    Stock::firstOrCreate(
                        ['product_id' => $item->product_id, 'outlet_id' => $purchaseOrder->outlet_id],
                        ['quantity' => 0]
                    )->increment('quantity', $item->quantity);
                }
                $purchaseOrder->update(['status' => 'received', 'received_at' => now()]);
            });
        }

        return back()->with('status', "{$purchaseOrder->reference} received into stock.");
    }
}
