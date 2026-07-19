<x-layouts.app title="Inventory" subtitle="Stock on hand by warehouse">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Stock table --}}
        <div class="xl:col-span-2">
            <x-card padding="p-0">
                <form method="GET" class="flex gap-3 px-5 py-4 border-b border-slate-100">
                    <input name="search" value="{{ request('search') }}" placeholder="Search product or SKU..."
                           class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20">
                    <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Search</button>
                </form>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="text-left text-xs uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <th class="px-5 py-3 font-medium">Product</th>
                            @foreach ($warehouses as $w)<th class="px-4 py-3 font-medium text-center">{{ $w->name }}</th>@endforeach
                            <th class="px-4 py-3 font-medium text-center">Total</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($products as $p)
                                @php $byWh = $p->batches->groupBy('warehouse_id')->map->sum('quantity'); @endphp
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-5 py-3"><p class="font-semibold text-slate-800">{{ $p->name }}</p><p class="text-xs text-slate-400 font-mono">{{ $p->sku }}</p></td>
                                    @foreach ($warehouses as $w)
                                        <td class="px-4 py-3 text-center tabular-nums text-slate-600">{{ number_format($byWh[$w->id] ?? 0) }}</td>
                                    @endforeach
                                    <td class="px-4 py-3 text-center tabular-nums font-semibold text-slate-800">{{ number_format($p->stock) }}</td>
                                    <td class="px-5 py-3"><x-pill :status="$p->status" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-4">{{ $products->links() }}</div>
            </x-card>
        </div>

        {{-- Actions --}}
        <div class="space-y-6">
            <x-card title="Adjust Stock" subtitle="Add or remove units (use negatives to remove)">
                <form method="POST" action="{{ route('inventory.adjust') }}" class="space-y-4">@csrf
                    <x-field label="Product" name="product_id" type="select" required :options="$productOptions" placeholder="Select product" />
                    <x-field label="Warehouse" name="warehouse_id" type="select" required :options="$warehouses->pluck('name','id')" placeholder="Select warehouse" />
                    <x-field label="Quantity (+/-)" name="quantity" type="number" required placeholder="e.g. 50 or -20" />
                    <x-field label="Note" name="note" placeholder="Reason for adjustment" />
                    <button class="w-full rounded-lg bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-700">Apply Adjustment</button>
                </form>
            </x-card>

            <x-card title="Transfer Stock" subtitle="Move units between warehouses">
                <form method="POST" action="{{ route('inventory.transfer') }}" class="space-y-4">@csrf
                    <x-field label="Product" name="product_id" type="select" required :options="$productOptions" placeholder="Select product" />
                    <x-field label="From" name="from_warehouse_id" type="select" required :options="$warehouses->pluck('name','id')" placeholder="Source warehouse" />
                    <x-field label="To" name="to_warehouse_id" type="select" required :options="$warehouses->pluck('name','id')" placeholder="Destination warehouse" />
                    <x-field label="Quantity" name="quantity" type="number" required placeholder="Units to move" />
                    <button class="w-full rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Transfer</button>
                </form>
            </x-card>
        </div>
    </div>
</x-layouts.app>
