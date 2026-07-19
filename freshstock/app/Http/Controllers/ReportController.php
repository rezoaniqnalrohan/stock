<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;

class ReportController extends Controller
{
    public function valuation()
    {
        $rows = Category::orderBy('name')->get()->map(function ($c) {
            $agg = Batch::join('products', 'products.id', '=', 'batches.product_id')
                ->where('products.category_id', $c->id)
                ->selectRaw('sum(batches.quantity) qty, sum(batches.quantity * products.cost) cost_val, sum(batches.quantity * products.price) retail_val')
                ->first();

            return (object) [
                'category' => $c->name,
                'qty' => (int) ($agg->qty ?? 0),
                'cost_val' => (float) ($agg->cost_val ?? 0),
                'retail_val' => (float) ($agg->retail_val ?? 0),
            ];
        });

        return view('reports.valuation', ['rows' => $rows, 'total' => $rows->sum('cost_val'), 'retail' => $rows->sum('retail_val')]);
    }

    public function movement()
    {
        $movements = StockMovement::with(['product', 'warehouse'])->latest()->paginate(25);

        return view('reports.movement', compact('movements'));
    }

    public function expiring()
    {
        $batches = Batch::with(['product', 'warehouse'])
            ->whereNotNull('expiry_date')->where('quantity', '>', 0)
            ->orderBy('expiry_date')->paginate(25);

        return view('reports.expiring', compact('batches'));
    }

    public function wastage()
    {
        $rows = StockMovement::with(['product', 'warehouse'])
            ->where('type', 'adjustment')->where('quantity', '<', 0)
            ->latest()->paginate(25);

        $totalUnits = abs((int) StockMovement::where('type', 'adjustment')->where('quantity', '<', 0)->sum('quantity'));

        return view('reports.wastage', compact('rows', 'totalUnits'));
    }
}
