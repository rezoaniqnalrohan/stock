<x-layouts.app title="Sales Orders" subtitle="Fulfil orders to customers">
    <x-slot:action>
        <a href="{{ route('sales-orders.create') }}" class="inline-flex items-center gap-2 rounded-full bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 shadow-sm">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg> New Order
        </a>
    </x-slot:action>

    <x-card padding="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs uppercase tracking-wider text-slate-400 border-b border-slate-100">
                    <th class="px-5 py-3 font-medium">Order #</th><th class="px-5 py-3 font-medium">Customer</th>
                    <th class="px-5 py-3 font-medium">Warehouse</th><th class="px-5 py-3 font-medium">Date</th>
                    <th class="px-5 py-3 font-medium">Items</th><th class="px-5 py-3 font-medium">Total</th>
                    <th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3"></th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($orders as $so)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3 font-mono text-slate-600">SO-{{ str_pad($so->id,4,'0',STR_PAD_LEFT) }}</td>
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $so->customer->name }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $so->warehouse->name }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $so->order_date->format('M j, Y') }}</td>
                            <td class="px-5 py-3 tabular-nums">{{ $so->items->count() }}</td>
                            <td class="px-5 py-3 tabular-nums font-semibold">${{ number_format($so->total,0) }}</td>
                            <td class="px-5 py-3"><x-pill :status="$so->status" /></td>
                            <td class="px-5 py-3 text-right"><a href="{{ route('sales-orders.show', $so) }}" class="text-sm font-medium text-teal-600 hover:text-teal-700">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-10 text-center text-slate-400">No sales orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $orders->links() }}</div>
    </x-card>
</x-layouts.app>
