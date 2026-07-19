<x-layouts.app title="Products" subtitle="{{ $products->total() }} SKUs in catalog">
    <x-slot:action>
        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 rounded-full bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 shadow-sm">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
            Add Product
        </a>
    </x-slot:action>

    <x-card padding="p-0">
        <form method="GET" class="flex flex-wrap items-center gap-3 px-5 py-4 border-b border-slate-100">
            <input name="search" value="{{ request('search') }}" placeholder="Search name or SKU..."
                   class="flex-1 min-w-48 rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20">
            <select name="category" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white">
                <option value="">All categories</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <th class="px-5 py-3 font-medium">Product</th>
                        <th class="px-5 py-3 font-medium">Category</th>
                        <th class="px-5 py-3 font-medium">Cost / Price</th>
                        <th class="px-5 py-3 font-medium">On Hand</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($products as $p)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3">
                                <p class="font-semibold text-slate-800">{{ $p->name }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ $p->sku }}</p>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $p->category->name }}</td>
                            <td class="px-5 py-3 tabular-nums text-slate-600">${{ number_format($p->cost,2) }} / <span class="font-medium text-slate-800">${{ number_format($p->price,2) }}</span></td>
                            <td class="px-5 py-3 tabular-nums font-medium">{{ number_format($p->stock) }} {{ $p->unit->abbreviation }}</td>
                            <td class="px-5 py-3"><x-pill :status="$p->status" /></td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('products.show', $p) }}" class="text-slate-400 hover:text-teal-600" title="View">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('products.edit', $p) }}" class="text-slate-400 hover:text-teal-600" title="Edit">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('products.destroy', $p) }}" onsubmit="return confirm('Delete this product?')">@csrf @method('DELETE')
                                        <button class="text-slate-400 hover:text-rose-500" title="Delete">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $products->links() }}</div>
    </x-card>
</x-layouts.app>
