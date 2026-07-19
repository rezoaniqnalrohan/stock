<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $products = Product::all();

        $totalSkus = $products->count();
        $stockValue = Batch::join('products', 'products.id', '=', 'batches.product_id')
            ->sum(\DB::raw('batches.quantity * products.cost'));
        $lowStock = $products->filter->isLowStock()->count();
        $openPos = PurchaseOrder::whereIn('status', ['draft', 'ordered'])->count();

        // Donut: stock quantity by category
        $byCategory = Category::withCount([])->get()->map(function ($c) {
            $qty = Batch::join('products', 'products.id', '=', 'batches.product_id')
                ->where('products.category_id', $c->id)->sum('batches.quantity');

            return ['name' => $c->name, 'color' => $c->color, 'qty' => (int) $qty];
        })->filter(fn ($r) => $r['qty'] > 0)->values();

        // Stacked bar + area: last 6 months of movement (in vs out) and cumulative value trend
        $months = collect(range(5, 0))->map(fn ($m) => Carbon::now()->subMonths($m));
        $movementIn = [];
        $movementOut = [];
        $labels = [];
        foreach ($months as $month) {
            $labels[] = $month->format('M');
            $movementIn[] = (int) StockMovement::where('type', 'in')
                ->whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->sum('quantity');
            $movementOut[] = (int) abs(StockMovement::where('type', 'out')
                ->whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->sum('quantity'));
        }

        // Area chart: inventory value trend (approximate cumulative net * avg cost)
        $avgCost = (float) ($products->avg('cost') ?: 1);
        $running = 0;
        $valueTrend = [];
        foreach ($months as $idx => $month) {
            $net = ($movementIn[$idx] ?? 0) - ($movementOut[$idx] ?? 0);
            $running += $net;
            $valueTrend[] = round(max(0, $running) * $avgCost);
        }

        $recentMovements = StockMovement::with(['product', 'warehouse'])
            ->latest()->take(8)->get();

        // Already-expired stock would sort to the top and crowd out the whole panel,
        // so keep this to what is actually still ahead. Expired batches live on /inventory/expiring.
        $expiringSoon = Batch::with(['product', 'warehouse'])
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', now())
            ->whereDate('expiry_date', '<=', now()->addDays(14))
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date')->take(6)->get();

        return view('dashboard', compact(
            'totalSkus', 'stockValue', 'lowStock', 'openPos',
            'byCategory', 'labels', 'movementIn', 'movementOut', 'valueTrend',
            'recentMovements', 'expiringSoon'
        ));
    }
}
