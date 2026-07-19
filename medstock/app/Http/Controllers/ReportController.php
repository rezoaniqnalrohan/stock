<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Product;
use App\Models\SalesOrderItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function valuation()
    {
        $products = Product::with('batches', 'category')->orderBy('name')->get();
        $totalValue = $products->sum(fn ($p) => $p->stock_value);
        $totalRetail = $products->sum(fn ($p) => $p->stock * $p->price);

        return view('reports.valuation', compact('products', 'totalValue', 'totalRetail'));
    }

    public function movement(Request $request)
    {
        $movements = StockMovement::with('product', 'warehouse', 'user')
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('reports.movement', compact('movements'));
    }

    public function expiring()
    {
        $batches = Batch::with('product', 'warehouse')
            ->where('quantity', '>', 0)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(120))
            ->orderBy('expiry_date')
            ->get();

        return view('reports.expiring', compact('batches'));
    }

    public function sales()
    {
        $byProduct = SalesOrderItem::selectRaw('product_id, sum(quantity) as qty, sum(quantity * price) as revenue')
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->get();

        $byCustomer = \App\Models\SalesOrder::selectRaw('customer_id, count(*) as orders, sum(total) as revenue')
            ->with('customer')
            ->groupBy('customer_id')
            ->orderByDesc('revenue')
            ->get();

        return view('reports.sales', compact('byProduct', 'byCustomer'));
    }
}
