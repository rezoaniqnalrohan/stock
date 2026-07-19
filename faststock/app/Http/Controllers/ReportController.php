<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index', [
            'ingredients' => Ingredient::orderBy('category')->orderBy('name')->get(),
            'movers' => $this->movers(),
        ]);
    }

    public function export(Request $request, string $type)
    {
        [$name, $header, $rows] = match ($type) {
            'valuation' => ['stock-valuation', ['Ingredient', 'Category', 'Unit', 'Stock', 'Unit cost (BDT)', 'Value (BDT)'],
                Ingredient::orderBy('category')->orderBy('name')->get()
                    ->map(fn ($i) => [$i->name, $i->category, $i->unit, $i->stock, $i->cost, round($i->stock * $i->cost, 2)])],
            'movers' => ['menu-movers-30d', ['Menu item', 'Price (BDT)', 'Sold (30d)', 'Revenue (BDT)'],
                $this->movers()->map(fn ($m) => [$m->name, $m->price, $m->sold, round($m->sold * $m->price, 2)])],
            'movements' => ['stock-movements', ['Date', 'Ingredient', 'Type', 'Qty', 'Unit cost', 'Supplier', 'Note', 'By'],
                MovementController::filtered($request)->with(['ingredient', 'supplier', 'user'])->latest()->get()
                    ->map(fn ($m) => [$m->created_at->format('Y-m-d H:i'), $m->ingredient->name, $m->type, $m->qty,
                        $m->unit_cost, $m->supplier?->name, $m->note, $m->user?->name])],
            default => abort(404),
        };

        return response()->streamDownload(function () use ($header, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $header);
            $rows->each(fn ($row) => fputcsv($out, $row));
            fclose($out);
        }, $name.'-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }

    private function movers()
    {
        return MenuItem::query()
            ->leftJoin('sale_items', 'sale_items.menu_item_id', '=', 'menu_items.id')
            ->leftJoin('sales', function ($join) {
                $join->on('sales.id', '=', 'sale_items.sale_id')->where('sales.created_at', '>=', now()->subDays(30));
            })
            ->groupBy('menu_items.id', 'menu_items.name', 'menu_items.price')
            ->orderByDesc('sold')
            ->get(['menu_items.id', 'menu_items.name', 'menu_items.price', DB::raw('coalesce(sum(case when sales.id is not null then sale_items.qty end), 0) as sold')]);
    }
}
