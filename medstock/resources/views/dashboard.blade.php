<x-layouts.app title="Dashboard" subtitle="Welcome back, {{ auth()->user()->name }}">
    <x-slot:action>
        <a href="{{ route('sales-orders.create') }}" class="hidden sm:inline-flex items-center gap-2 rounded-full bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 shadow-sm">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
            New Order
        </a>
    </x-slot:action>

    {{-- KPI tiles --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-kpi label="Total SKUs" :value="number_format($totalSkus)" tone="brand"
               icon="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14L4 7m8 4v10M4 7v10l8 4" />
        <x-kpi label="Stock Value" :value="'$'.number_format($stockValue, 0)" tone="sky"
               icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2" />
        <x-kpi label="Low / Out of Stock" :value="$lowStock" tone="amber"
               icon="M12 9v2m0 4h.01M5 19h14a2 2 0 001.84-2.75L13.74 4a2 2 0 00-3.48 0L3.16 16.25A2 2 0 005 19z" />
        <x-kpi label="Open Orders" :value="$openOrders" tone="violet"
               icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
    </div>

    <div class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Main column --}}
        <div class="xl:col-span-2 space-y-6">
            {{-- Reporting table --}}
            <x-card title="Reporting" subtitle="Top items by stock value" padding="p-0">
                <x-slot:actions>
                    <a href="{{ route('reports.movement') }}" class="text-sm font-medium text-teal-600 hover:text-teal-700">View all</a>
                </x-slot:actions>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                <th class="px-5 py-3 font-medium">Product</th>
                                <th class="px-5 py-3 font-medium">On Hand</th>
                                <th class="px-5 py-3 font-medium">Value</th>
                                <th class="px-5 py-3 font-medium">Trend</th>
                                <th class="px-5 py-3 font-medium">Status</th>
                                <th class="px-5 py-3 font-medium">Last 12d</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($reporting as $row)
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-5 py-3">
                                        <a href="{{ route('products.show', $row['product']) }}" class="font-semibold text-slate-800 hover:text-teal-600">{{ $row['product']->name }}</a>
                                        <p class="text-xs text-slate-400 font-mono">{{ $row['product']->sku }}</p>
                                    </td>
                                    <td class="px-5 py-3 tabular-nums font-medium text-slate-700">{{ number_format($row['product']->stock) }} {{ $row['product']->unit->abbreviation }}</td>
                                    <td class="px-5 py-3 tabular-nums font-semibold text-slate-800">${{ number_format($row['product']->stock_value, 0) }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $row['trend'] >= 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $row['trend'] >= 0 ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                            {{ abs($row['trend']) }}%
                                        </span>
                                    </td>
                                    <td class="px-5 py-3"><x-pill :status="$row['status']" /></td>
                                    <td class="px-5 py-3"><x-sparkline :data="$row['spark']" :color="$row['trend'] >= 0 ? '#0d9488' : '#f59e0b'" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            {{-- Movement chart --}}
            <x-card title="Stock Movement" subtitle="Units received vs shipped · last 14 days">
                <div class="h-64"><canvas id="movementChart"></canvas></div>
            </x-card>

            {{-- Ready to ship --}}
            <x-card title="Ready to Ship" subtitle="Picked &amp; packed sales orders" padding="p-0">
                <x-slot:actions>
                    <a href="{{ route('sales-orders.index') }}" class="text-sm font-medium text-teal-600 hover:text-teal-700">All orders</a>
                </x-slot:actions>
                <div class="divide-y divide-slate-50">
                    @forelse ($readyToShip as $so)
                        <a href="{{ route('sales-orders.show', $so) }}" class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50/60">
                            <div class="h-10 w-10 rounded-lg bg-orange-50 text-orange-500 flex items-center justify-center">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-slate-800">Order #{{ str_pad($so->id, 5, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-xs text-slate-400">{{ $so->customer->name }} · {{ $so->items->count() }} items</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-slate-800 tabular-nums">${{ number_format($so->total, 0) }}</p>
                                <x-pill :status="$so->status" />
                            </div>
                        </a>
                    @empty
                        <p class="px-5 py-8 text-center text-sm text-slate-400">No orders waiting to ship.</p>
                    @endforelse
                </div>
            </x-card>
        </div>

        {{-- Right sidebar: Recommended reorders --}}
        <div class="space-y-6">
            <x-card padding="p-0">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <div>
                        <h2 class="font-semibold text-slate-900">Recommended Reorders</h2>
                        <p class="text-xs text-slate-400">At or below reorder point</p>
                    </div>
                    <span class="inline-flex items-center justify-center h-6 min-w-6 px-2 rounded-full bg-rose-50 text-rose-600 text-xs font-bold">{{ $reorders->count() }}</span>
                </div>
                <div class="p-3 space-y-2">
                    @forelse ($reorders as $p)
                        <div class="rounded-xl border border-slate-100 p-3.5 hover:border-teal-200 transition">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-800 truncate">{{ $p->name }}</p>
                                    <p class="text-xs text-slate-400">On hand: {{ $p->stock }} · Reorder at {{ $p->reorder_point }}</p>
                                </div>
                                <x-pill :status="$p->status" />
                            </div>
                            <div class="mt-3 flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                                <div>
                                    <p class="text-[11px] text-slate-400">Cost / {{ $p->unit->abbreviation }}</p>
                                    <p class="font-semibold text-slate-800">${{ number_format($p->cost, 2) }}</p>
                                </div>
                                <a href="{{ route('purchase-orders.create') }}" class="rounded-lg bg-teal-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-teal-700">Reorder</a>
                            </div>
                        </div>
                    @empty
                        <p class="px-2 py-8 text-center text-sm text-slate-400">Everything is well stocked.</p>
                    @endforelse
                </div>
            </x-card>

            {{-- Expiry alert --}}
            <x-card title="Expiry Watch">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-900">{{ $expiringSoon }}</p>
                        <p class="text-sm text-slate-400">batches expiring within 90 days</p>
                    </div>
                </div>
                <a href="{{ route('reports.expiring') }}" class="mt-4 block text-center rounded-lg border border-orange-200 bg-orange-50 py-2 text-sm font-semibold text-orange-700 hover:bg-orange-100">Review expiring stock</a>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script>
        new Chart(document.getElementById('movementChart'), {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [
                    { label: 'Received', data: @json($inSeries), borderColor: '#0d9488', backgroundColor: 'rgba(13,148,136,.12)', fill: true, tension: .4, borderWidth: 2, pointRadius: 0 },
                    { label: 'Shipped', data: @json($outSeries), borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,.10)', fill: true, tension: .4, borderWidth: 2, pointRadius: 0 },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 16 } } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#94a3b8', maxRotation: 0, autoSkip: true, maxTicksLimit: 7 } },
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8' } }
                }
            }
        });
    </script>
    @endpush
</x-layouts.app>
