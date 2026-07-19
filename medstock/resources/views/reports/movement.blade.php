<x-layouts.app title="Stock Movement" subtitle="Full audit trail of inventory changes">
    <x-card padding="p-0">
        <form method="GET" class="flex gap-3 px-5 py-4 border-b border-slate-100">
            <select name="type" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white">
                <option value="">All movement types</option>
                @foreach (['in'=>'Stock In','out'=>'Stock Out','adjust'=>'Adjustment','transfer'=>'Transfer'] as $k=>$v)
                    <option value="{{ $k }}" @selected(request('type')===$k)>{{ $v }}</option>
                @endforeach
            </select>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs uppercase tracking-wider text-slate-400 border-b border-slate-100">
                    <th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Product</th>
                    <th class="px-5 py-3 font-medium">Warehouse</th><th class="px-5 py-3 font-medium">Type</th>
                    <th class="px-5 py-3 font-medium text-right">Qty</th><th class="px-5 py-3 font-medium">Reference</th>
                    <th class="px-5 py-3 font-medium">By</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @php $typeColors=['in'=>'text-emerald-600','out'=>'text-rose-500','adjust'=>'text-amber-600','transfer'=>'text-sky-600']; @endphp
                    @forelse ($movements as $m)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3 text-slate-500 whitespace-nowrap">{{ $m->created_at->format('M j, Y g:i A') }}</td>
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $m->product->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $m->warehouse->name ?? '—' }}</td>
                            <td class="px-5 py-3"><span class="text-xs font-semibold uppercase {{ $typeColors[$m->type] ?? 'text-slate-500' }}">{{ $m->type }}</span></td>
                            <td class="px-5 py-3 text-right tabular-nums font-medium {{ $m->quantity < 0 ? 'text-rose-500' : 'text-emerald-600' }}">{{ $m->quantity > 0 ? '+' : '' }}{{ number_format($m->quantity) }}</td>
                            <td class="px-5 py-3 font-mono text-xs text-slate-500">{{ $m->reference }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $m->user->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">No movements recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $movements->links() }}</div>
    </x-card>
</x-layouts.app>
