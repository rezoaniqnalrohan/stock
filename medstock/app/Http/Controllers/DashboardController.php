<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $products = Product::with('batches', 'category', 'unit')->get();

        $totalSkus = $products->count();
        $stockValue = $products->sum(fn ($p) => $p->stock_value);
        $lowStock = $products->filter(fn ($p) => in_array($p->status, ['low', 'out']))->count();
        $openOrders = SalesOrder::whereIn('status', ['pending', 'picked', 'packed'])->count();

        // Reporting table: top movers with 12-point sparkline of daily out-movements.
        $reporting = $products->sortByDesc('stock_value')->take(6)->map(function ($p) {
            $spark = [];
            for ($d = 11; $d >= 0; $d--) {
                $spark[] = (int) abs(StockMovement::where('product_id', $p->id)
                    ->where('type', 'out')
                    ->whereDate('created_at', now()->subDays($d))
                    ->sum('quantity'));
            }
            $first = array_slice($spark, 0, 6);
            $last = array_slice($spark, 6);
            $trend = array_sum($first) > 0
                ? round((array_sum($last) - array_sum($first)) / max(array_sum($first), 1) * 100)
                : 0;

            return [
                'product' => $p,
                'spark' => $spark,
                'trend' => $trend,
                'status' => $p->status,
            ];
        })->values();

        // Recommended reorders: at or below reorder point.
        $reorders = $products->filter(fn ($p) => $p->status !== 'in')
            ->sortBy('stock')
            ->take(6)
            ->values();

        // Ready to ship (packed / picked sales orders).
        $readyToShip = SalesOrder::with('customer', 'items')
            ->whereIn('status', ['picked', 'packed'])
            ->latest('order_date')->take(3)->get();

        // Movement chart: last 14 days in vs out.
        $labels = [];
        $inSeries = [];
        $outSeries = [];
        for ($d = 13; $d >= 0; $d--) {
            $day = now()->subDays($d);
            $labels[] = $day->format('M j');
            $inSeries[] = (int) StockMovement::where('type', 'in')->whereDate('created_at', $day)->sum('quantity');
            $outSeries[] = (int) abs(StockMovement::where('type', 'out')->whereDate('created_at', $day)->sum('quantity'));
        }

        $expiringSoon = Batch::where('quantity', '>', 0)
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays(90)])
            ->count();

        return view('dashboard', compact(
            'totalSkus', 'stockValue', 'lowStock', 'openOrders',
            'reporting', 'reorders', 'readyToShip',
            'labels', 'inSeries', 'outSeries', 'expiringSoon'
        ));
    }
}
