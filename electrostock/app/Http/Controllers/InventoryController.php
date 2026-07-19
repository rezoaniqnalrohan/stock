<?php

namespace App\Http\Controllers;

use App\Models\Adjustment;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $outletId = $request->get('outlet', 'all');
        $onlyLow = $request->boolean('low');

        $stocks = Stock::with(['product.brand', 'outlet'])
            ->join('products', 'products.id', '=', 'stocks.product_id')
            ->when($outletId !== 'all', fn ($q) => $q->where('stocks.outlet_id', $outletId))
            ->when($onlyLow, fn ($q) => $q->whereColumn('stocks.quantity', '<=', 'products.reorder_point'))
            ->when($request->search, fn ($q, $s) => $q->where('products.name', 'like', "%{$s}%"))
            ->orderBy('products.name')->select('stocks.*')
            ->paginate(15)->withQueryString();

        return view('inventory.index', [
            'stocks' => $stocks,
            'outlets' => Outlet::orderBy('name')->get(),
            'outletId' => $outletId,
            'onlyLow' => $onlyLow,
            'adjustments' => Adjustment::with(['product', 'outlet', 'user'])->latest()->take(8)->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function adjust(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'outlet_id' => ['required', 'exists:outlets,id'],
            'delta' => ['required', 'integer', 'not_in:0'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $stock = Stock::firstOrCreate(
            ['product_id' => $data['product_id'], 'outlet_id' => $data['outlet_id']],
            ['quantity' => 0]
        );
        $stock->increment('quantity', $data['delta']);

        Adjustment::create($data + ['user_id' => $request->user()->id]);

        return back()->with('status', 'Stock adjusted.');
    }
}
