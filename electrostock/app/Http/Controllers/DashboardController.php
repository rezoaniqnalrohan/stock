<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $range = in_array($request->get('range'), ['today', 'week', 'month']) ? $request->get('range') : 'month';
        $outletId = $request->get('outlet', 'all');

        [$start, $prevStart, $prevEnd] = match ($range) {
            'today' => [Carbon::today(), Carbon::today()->subDay(), Carbon::today()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->startOfWeek()->subWeek(), Carbon::now()->startOfWeek()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->startOfMonth()->subMonth(), Carbon::now()->startOfMonth()],
        };

        $scoped = fn ($q) => $outletId !== 'all' ? $q->where('outlet_id', $outletId) : $q;

        $sales = $scoped(Sale::query())->where('created_at', '>=', $start);
        $totalSold = (float) (clone $sales)->sum('total');
        $count = (clone $sales)->count();
        $items = (int) (clone $sales)->sum('items_count');
        $avgSale = $count ? $totalSold / $count : 0;
        $avgItems = $count ? $items / $count : 0;

        $prevTotal = (float) $scoped(Sale::query())->whereBetween('created_at', [$prevStart, $prevEnd])->sum('total');

        // Sales over time (daily buckets across current range window)
        $chartStart = $range === 'today' ? Carbon::today() : ($range === 'week' ? Carbon::now()->startOfWeek() : Carbon::now()->subDays(29));
        $daily = $scoped(Sale::query())->where('created_at', '>=', $chartStart)
            ->selectRaw('date(created_at) d, sum(total) t')
            ->groupBy('d')->pluck('t', 'd');

        $labels = [];
        $series = [];
        for ($c = $chartStart->copy(); $c <= Carbon::today(); $c->addDay()) {
            $labels[] = $c->format('j M');
            $series[] = round((float) ($daily[$c->format('Y-m-d')] ?? 0), 2);
        }

        $lowStock = Stock::whereColumn('quantity', '<=', 'products.reorder_point')
            ->join('products', 'products.id', '=', 'stocks.product_id')
            ->when($outletId !== 'all', fn ($q) => $q->where('stocks.outlet_id', $outletId))
            ->count();

        $pendingTransfers = Transfer::with(['product', 'fromOutlet', 'toOutlet'])
            ->where('status', 'pending')->latest()->get();

        $dispatchedPOs = PurchaseOrder::with(['items.product', 'supplier'])
            ->where('status', 'dispatched')->latest()->get();

        return view('dashboard', compact(
            'range', 'outletId', 'totalSold', 'avgSale', 'avgItems', 'count', 'prevTotal',
            'labels', 'series', 'lowStock', 'pendingTransfers', 'dispatchedPOs'
        ) + ['outlets' => Outlet::orderBy('name')->get()]);
    }
}
