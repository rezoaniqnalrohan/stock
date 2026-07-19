<x-layouts.app title="Stock Valuation" subtitle="Inventory value at cost and retail">
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <x-kpi label="Value at Cost" :value="'$'.number_format($totalValue,0)" tone="brand" icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2" />
        <x-kpi label="Value at Retail" :value="'$'.number_format($totalRetail,0)" tone="sky" icon="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 5h12" />
        <x-kpi label="Potential Margin" :value="'$'.number_format($totalRetail-$totalValue,0)" tone="violet" icon="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
    </div>

    <x-card padding="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs uppercase tracking-wider text-slate-400 border-b border-slate-100">
                    <th class="px-5 py-3 font-medium">Product</th><th class="px-5 py-3 font-medium">Category</th>
                    <th class="px-5 py-3 font-medium text-right">On Hand</th><th class="px-5 py-3 font-medium text-right">Cost</th>
                    <th class="px-5 py-3 font-medium text-right">Value</th><th class="px-5 py-3 font-medium text-right">Retail Value</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($products as $p)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3"><p class="font-medium text-slate-800">{{ $p->name }}</p><p class="text-xs text-slate-400 font-mono">{{ $p->sku }}</p></td>
                            <td class="px-5 py-3 text-slate-600">{{ $p->category->name }}</td>
                            <td class="px-5 py-3 text-right tabular-nums">{{ number_format($p->stock) }}</td>
                            <td class="px-5 py-3 text-right tabular-nums">${{ number_format($p->cost,2) }}</td>
                            <td class="px-5 py-3 text-right tabular-nums font-semibold text-slate-800">${{ number_format($p->stock_value,2) }}</td>
                            <td class="px-5 py-3 text-right tabular-nums text-slate-600">${{ number_format($p->stock * $p->price,2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot><tr class="border-t border-slate-100 bg-slate-50/50 font-bold text-slate-900">
                    <td colspan="4" class="px-5 py-3 text-right">Totals</td>
                    <td class="px-5 py-3 text-right tabular-nums">${{ number_format($totalValue,2) }}</td>
                    <td class="px-5 py-3 text-right tabular-nums">${{ number_format($totalRetail,2) }}</td>
                </tr></tfoot>
            </table>
        </div>
    </x-card>
</x-layouts.app>
