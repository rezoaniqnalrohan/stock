<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $outletId = $request->get('outlet', optional(Outlet::where('is_warehouse', false)->first())->id);

        $products = Product::with('brand')
            ->leftJoin('stocks', fn ($j) => $j->on('stocks.product_id', '=', 'products.id')->where('stocks.outlet_id', $outletId))
            ->select('products.*', DB::raw('coalesce(stocks.quantity, 0) as qty'))
            ->orderBy('products.name')->get();

        return view('pos.index', [
            'products' => $products,
            'outlets' => Outlet::where('is_warehouse', false)->orderBy('name')->get(),
            'outletId' => (int) $outletId,
            'customers' => Customer::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'outlet_id' => ['required', 'exists:outlets,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $sale = DB::transaction(function () use ($data, $request) {
            $sale = Sale::create([
                'reference' => 'S-' . str_pad((int) Sale::max('id') + 1, 5, '0', STR_PAD_LEFT),
                'outlet_id' => $data['outlet_id'],
                'user_id' => $request->user()->id,
                'customer_id' => $data['customer_id'] ?? null,
            ]);

            $total = 0;
            $count = 0;
            foreach ($data['items'] as $line) {
                $product = Product::findOrFail($line['product_id']);
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $line['quantity'],
                    'price' => $product->price,
                ]);
                Stock::firstOrCreate(
                    ['product_id' => $product->id, 'outlet_id' => $data['outlet_id']],
                    ['quantity' => 0]
                )->decrement('quantity', $line['quantity']);
                $total += $product->price * $line['quantity'];
                $count += $line['quantity'];
            }

            $sale->update(['total' => $total, 'items_count' => $count]);
            if ($sale->customer_id) {
                Customer::whereKey($sale->customer_id)->increment('total_spent', $total);
            }

            return $sale;
        });

        return redirect()->route('pos')->with('status', "Sale {$sale->reference} recorded · \${$sale->total}.");
    }
}
