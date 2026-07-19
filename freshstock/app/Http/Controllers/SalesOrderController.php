<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    public function index()
    {
        return view('sales-orders.index', [
            'orders' => SalesOrder::with(['customer', 'warehouse'])->latest()->paginate(12),
        ]);
    }

    public function create()
    {
        return view('sales-orders.form', [
            'customers' => Customer::orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $so = SalesOrder::create([
            'order_number' => 'SO-'.str_pad((string) (SalesOrder::max('id') + 1), 4, '0', STR_PAD_LEFT),
            'customer_id' => $data['customer_id'],
            'warehouse_id' => $data['warehouse_id'],
            'status' => 'pending',
            'order_date' => now(),
            'total' => 0,
        ]);

        $total = 0;
        foreach ($data['items'] as $item) {
            $so->items()->create($item);
            $total += $item['quantity'] * $item['unit_price'];
        }
        $so->update(['total' => $total]);

        return redirect('/orders/'.$so->id)->with('status', 'Order created.');
    }

    public function show(SalesOrder $order)
    {
        $order->load(['customer', 'warehouse', 'items.product']);

        return view('sales-orders.show', compact('order'));
    }

    // Fulfil: decrement stock (FEFO) and log out movements, mark fulfilled.
    public function fulfill(SalesOrder $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('status', 'Order already processed.');
        }

        $order->load('items');
        foreach ($order->items as $item) {
            $remaining = $item->quantity;
            $batches = Batch::where('product_id', $item->product_id)
                ->where('warehouse_id', $order->warehouse_id)
                ->where('quantity', '>', 0)
                ->orderByRaw('expiry_date is null, expiry_date')->get();

            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }
                $take = min($remaining, $batch->quantity);
                $batch->decrement('quantity', $take);
                $remaining -= $take;
            }

            StockMovement::create([
                'product_id' => $item->product_id,
                'warehouse_id' => $order->warehouse_id,
                'type' => 'out',
                'quantity' => -$item->quantity,
                'reference' => $order->order_number,
                'note' => 'Fulfilled sales order',
            ]);
        }

        $order->update(['status' => 'fulfilled']);

        return back()->with('status', 'Order fulfilled and stock decremented.');
    }
}
