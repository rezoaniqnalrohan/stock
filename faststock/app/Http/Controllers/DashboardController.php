<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stockValue = Ingredient::sum(DB::raw('stock * cost'));
        $ingredientCount = Ingredient::count();
        $todaySales = Sale::whereDate('created_at', today())->sum('total');
        $todayOrders = Sale::whereDate('created_at', today())->count();

        $thisWeek = Sale::where('created_at', '>=', today()->subDays(6))->sum('total');
        $lastWeek = Sale::whereBetween('created_at', [today()->subDays(13), today()->subDays(7)->endOfDay()])->sum('total');
        $weekDelta = $lastWeek > 0 ? round(($thisWeek - $lastWeek) / $lastWeek * 100) : null;

        $low = Ingredient::whereColumn('stock', '<=', 'reorder_level')->orderByRaw('stock - reorder_level')->get();

        // ponytail: expiry shown per purchase batch, no FIFO depletion tracking; add a batches table if precision matters
        $expiring = StockMovement::with('ingredient')
            ->where('type', 'purchase')
            ->whereBetween('expiry_date', [today(), today()->addDays(7)])
            ->orderBy('expiry_date')
            ->get();

        $days = collect(range(13, 0))->map(fn ($d) => today()->subDays($d));
        $salesByDay = Sale::where('created_at', '>=', today()->subDays(13))->get()
            ->groupBy(fn ($s) => $s->created_at->format('Y-m-d'))
            ->map(fn ($g) => $g->sum('total'));
        $chart = [
            'labels' => $days->map(fn ($d) => $d->format('d M')),
            'data' => $days->map(fn ($d) => round($salesByDay[$d->format('Y-m-d')] ?? 0)),
        ];

        $byCategory = Ingredient::selectRaw('category, sum(stock * cost) as value')
            ->groupBy('category')->orderByDesc('value')->having('value', '>', 0)->get();

        $topSellers = SaleItem::selectRaw('menu_item_id, sum(qty) as sold, sum(qty * price) as revenue')
            ->whereRelation('sale', 'created_at', '>=', now()->subDays(7))
            ->groupBy('menu_item_id')->orderByDesc('sold')->with('menuItem')->limit(5)->get();

        return view('dashboard', compact(
            'stockValue', 'ingredientCount', 'todaySales', 'todayOrders',
            'thisWeek', 'weekDelta', 'low', 'expiring', 'chart', 'byCategory', 'topSellers'
        ));
    }
}
