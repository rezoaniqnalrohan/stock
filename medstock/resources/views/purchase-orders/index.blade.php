<x-layouts.app title="Purchase Orders" subtitle="Restock from suppliers">
    <x-slot:action>
        <a href="{{ route('purchase-orders.create') }}" class="inline-flex items-center gap-2 rounded-full bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 shadow-sm">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg> New PO
        </a>
    </x-slot:action>

    <x-card padding="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs uppercase tracking-wider text-slate-400 border-b border-slate-100">
                    <th class="px-5 py-3 font-medium">PO #</th><th class="px-5 py-3 font-medium">Supplier</th>
                    <th class="px-5 py-3 font-medium">Warehouse</th><th class="px-5 py-3 font-medium">Date</th>
                    <th class="px-5 py-3 font-medium">Items</th><th class="px-5 py-3 font-medium">Total</th>
                    <th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3"></th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($orders as $po)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3 font-mono text-slate-600">PO-{{ str_pad($po->id,4,'0',STR_PAD_LEFT) }}</td>
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $po->supplier->name }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $po->warehouse->name }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $po->order_date->format('M j, Y') }}</td>
                            <td class="px-5 py-3 tabular-nums">{{ $po->items->count() }}</td>
                            <td class="px-5 py-3 tabular-nums font-semibold">${{ number_format($po->total,0) }}</td>
                            <td class="px-5 py-3"><x-pill :status="$po->status" /></td>
                            <td class="px-5 py-3 text-right"><a href="{{ route('purchase-orders.show', $po) }}" class="text-sm font-medium text-teal-600 hover:text-teal-700">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-10 text-center text-slate-400">No purchase orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $orders->links() }}</div>
    </x-card>
</x-layouts.app>
