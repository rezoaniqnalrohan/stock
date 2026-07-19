<x-layouts.app title="Expiring Soon" subtitle="Batches expiring within 120 days">
    <x-card padding="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs uppercase tracking-wider text-slate-400 border-b border-slate-100">
                    <th class="px-5 py-3 font-medium">Product</th><th class="px-5 py-3 font-medium">Warehouse</th>
                    <th class="px-5 py-3 font-medium">Lot</th><th class="px-5 py-3 font-medium">Expiry</th>
                    <th class="px-5 py-3 font-medium text-right">Qty</th><th class="px-5 py-3 font-medium">Days Left</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($batches as $b)
                        @php $days = (int) now()->startOfDay()->diffInDays($b->expiry_date, false); $urgent = $days <= 30; @endphp
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3"><a href="{{ route('products.show', $b->product) }}" class="font-medium text-slate-800 hover:text-teal-600">{{ $b->product->name }}</a></td>
                            <td class="px-5 py-3 text-slate-600">{{ $b->warehouse->name }}</td>
                            <td class="px-5 py-3 font-mono text-xs text-slate-500">{{ $b->lot_number ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-700">{{ $b->expiry_date->format('M j, Y') }}</td>
                            <td class="px-5 py-3 text-right tabular-nums font-medium">{{ number_format($b->quantity) }}</td>
                            <td class="px-5 py-3"><span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $urgent ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700' }}">{{ $days }} days</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">No batches expiring soon.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
