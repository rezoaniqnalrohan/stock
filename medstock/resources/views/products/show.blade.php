<x-layouts.app title="{{ $product->name }}" subtitle="{{ $product->sku }}">
    <x-slot:action>
        <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center gap-2 rounded-full bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 shadow-sm">Edit</a>
    </x-slot:action>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Details">
                <dl class="grid sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                    <div><dt class="text-slate-400">Category</dt><dd class="font-medium text-slate-800">{{ $product->category->name }}</dd></div>
                    <div><dt class="text-slate-400">Unit of Measure</dt><dd class="font-medium text-slate-800">{{ $product->unit->name }} ({{ $product->unit->abbreviation }})</dd></div>
                    <div><dt class="text-slate-400">Supplier</dt><dd class="font-medium text-slate-800">{{ $product->supplier?->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Barcode / Lot</dt><dd class="font-medium text-slate-800 font-mono">{{ $product->barcode ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Cost / Price</dt><dd class="font-medium text-slate-800">${{ number_format($product->cost,2) }} / ${{ number_format($product->price,2) }}</dd></div>
                    <div><dt class="text-slate-400">Reorder Point</dt><dd class="font-medium text-slate-800">{{ $product->reorder_point }} {{ $product->unit->abbreviation }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-400">Description</dt><dd class="text-slate-700">{{ $product->description ?? '—' }}</dd></div>
                </dl>
            </x-card>

            <x-card title="Batches &amp; Expiry" subtitle="Lots on hand across warehouses" padding="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="text-left text-xs uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <th class="px-5 py-3 font-medium">Warehouse</th><th class="px-5 py-3 font-medium">Lot</th>
                            <th class="px-5 py-3 font-medium">Expiry</th><th class="px-5 py-3 font-medium">Qty</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse ($product->batches->where('quantity','>',0) as $b)
                                @php $soon = $b->expiry_date && $b->expiry_date->lte(now()->addDays(90)); @endphp
                                <tr>
                                    <td class="px-5 py-3 text-slate-700">{{ $b->warehouse->name }}</td>
                                    <td class="px-5 py-3 font-mono text-xs text-slate-500">{{ $b->lot_number ?? '—' }}</td>
                                    <td class="px-5 py-3 {{ $soon ? 'text-orange-600 font-medium' : 'text-slate-600' }}">
                                        {{ $b->expiry_date?->format('M j, Y') ?? '—' }} @if($soon)<span class="ml-1 text-[10px] uppercase">soon</span>@endif
                                    </td>
                                    <td class="px-5 py-3 tabular-nums font-medium">{{ number_format($b->quantity) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-8 text-center text-slate-400">No stock on hand.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card>
                <p class="text-sm text-slate-400">Total On Hand</p>
                <p class="mt-1 text-3xl font-bold text-slate-900">{{ number_format($product->stock) }} <span class="text-base font-medium text-slate-400">{{ $product->unit->abbreviation }}</span></p>
                <div class="mt-3"><x-pill :status="$product->status" /></div>
                <div class="mt-5 rounded-xl bg-slate-50 p-4">
                    <p class="text-sm text-slate-400">Stock Value (at cost)</p>
                    <p class="text-xl font-bold text-slate-800">${{ number_format($product->stock_value, 2) }}</p>
                </div>
                <a href="{{ route('inventory.index') }}" class="mt-4 block text-center rounded-lg border border-slate-300 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Adjust in Inventory</a>
            </x-card>
        </div>
    </div>
</x-layouts.app>
