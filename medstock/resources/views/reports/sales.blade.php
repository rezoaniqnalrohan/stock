<x-layouts.app title="Sales Report" subtitle="Revenue by product and customer">
    <div class="grid lg:grid-cols-2 gap-6">
        <x-card title="Top Products" subtitle="By revenue" padding="p-0">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs uppercase tracking-wider text-slate-400 border-b border-slate-100">
                    <th class="px-5 py-3 font-medium">Product</th><th class="px-5 py-3 font-medium text-right">Units</th><th class="px-5 py-3 font-medium text-right">Revenue</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($byProduct as $row)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $row->product->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-right tabular-nums">{{ number_format($row->qty) }}</td>
                            <td class="px-5 py-3 text-right tabular-nums font-semibold text-slate-800">${{ number_format($row->revenue,2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-10 text-center text-slate-400">No sales yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>

        <x-card title="Top Customers" subtitle="By revenue" padding="p-0">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs uppercase tracking-wider text-slate-400 border-b border-slate-100">
                    <th class="px-5 py-3 font-medium">Customer</th><th class="px-5 py-3 font-medium text-right">Orders</th><th class="px-5 py-3 font-medium text-right">Revenue</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($byCustomer as $row)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $row->customer->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-right tabular-nums">{{ $row->orders }}</td>
                            <td class="px-5 py-3 text-right tabular-nums font-semibold text-slate-800">${{ number_format($row->revenue,2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-10 text-center text-slate-400">No sales yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>
    </div>
</x-layouts.app>
