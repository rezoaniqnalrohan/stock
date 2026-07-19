<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $salesByOutlet = Outlet::where('is_warehouse', false)
            ->withSum('sales as revenue', 'total')
            ->withCount('sales')
            ->orderByDesc('revenue')->get();

        $bestSellers = SaleItem::select('product_id', DB::raw('sum(quantity) units'), DB::raw('sum(quantity*price) revenue'))
            ->with('product')
            ->groupBy('product_id')->orderByDesc('units')->take(8)->get();

        $stockOnHand = Stock::join('products', 'products.id', '=', 'stocks.product_id')
            ->join('outlets', 'outlets.id', '=', 'stocks.outlet_id')
            ->selectRaw('outlets.name outlet, sum(stocks.quantity) units, sum(stocks.quantity*products.cost) cost_value, sum(stocks.quantity*products.price) retail_value')
            ->groupBy('outlets.id', 'outlets.name')->orderByDesc('units')->get();

        $transfers = Transfer::with(['product', 'fromOutlet', 'toOutlet'])->latest()->take(10)->get();

        return view('reports.index', compact('salesByOutlet', 'bestSellers', 'stockOnHand', 'transfers'));
    }
}
