<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    // Fulfilment stages, in order. Shipping decrements stock.
    private const FLOW = ['pending', 'picked', 'packed', 'shipped'];

    public function index()
    {
        return view('sales-orders.index', [
            'orders' => SalesOrder::with('customer', 'warehouse', 'items')->latest('order_date')->paginate(12),
        ]);
    }

    public function create()
    {
        return view('sales-orders.create', [
            'customers' => Customer::orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('name')->get(),
            'products' => Product::with('batches')->orderBy('name')->get()
                ->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'price' => (float) $p->price, 'stock' => $p->stock]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'order_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);

        $order = DB::transaction(function () use ($data) {
            $order = SalesOrder::create([
                'customer_id' => $data['customer_id'],
                'warehouse_id' => $data['warehouse_id'],
                'order_date' => $data['order_date'],
                'status' => 'pending',
                'user_id' => Auth::id(),
            ]);
            $total = 0;
            foreach ($data['items'] as $item) {
                $order->items()->create($item);
                $total += $item['quantity'] * $item['price'];
            }
            $order->update(['total' => $total]);

            return $order;
        });

        return redirect()->route('sales-orders.show', $order)->with('status', 'Sales order created.');
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load('customer', 'warehouse', 'items.product');

        return view('sales-orders.show', ['order' => $salesOrder, 'flow' => self::FLOW]);
    }

    // Advance one fulfilment stage; decrement stock when it reaches "shipped".
    public function advance(SalesOrder $salesOrder)
    {
        $i = array_search($salesOrder->status, self::FLOW, true);
        if ($i === false || $i >= count(self::FLOW) - 1) {
            return back()->withErrors(['status' => 'Order is already shipped.']);
        }

        $next = self::FLOW[$i + 1];

        if ($next === 'shipped') {
            // Check availability before shipping.
            foreach ($salesOrder->items as $item) {
                $available = Batch::where('product_id', $item->product_id)
                    ->where('warehouse_id', $salesOrder->warehouse_id)->sum('quantity');
                if ($available < $item->quantity) {
                    return back()->withErrors(['status' => "Not enough stock for {$item->product->name} ($available available)."]);
                }
            }
            DB::transaction(function () use ($salesOrder, $next) {
                foreach ($salesOrder->items as $item) {
                    Batch::move($item->product_id, $salesOrder->warehouse_id, -$item->quantity, 'out', 'SO-'.$salesOrder->id, 'Shipped');
                }
                $salesOrder->update(['status' => $next]);
            });
        } else {
            $salesOrder->update(['status' => $next]);
        }

        return back()->with('status', "Order marked as $next.");
    }

    public function destroy(SalesOrder $salesOrder)
    {
        $salesOrder->delete();

        return redirect()->route('sales-orders.index')->with('status', 'Sales order deleted.');
    }
}
