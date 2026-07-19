@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="grid gap-5 xl:grid-cols-[1fr_320px]">
    <div class="min-w-0 space-y-5">
        {{-- Stat cards --}}
        <div class="grid gap-5 sm:grid-cols-3">
            <div class="rounded-3xl bg-ink p-6 text-white">
                <p class="text-sm text-gray-400">Stock value</p>
                <p class="num mt-2 text-3xl font-bold tracking-tight">৳{{ number_format($stockValue) }}</p>
                <p class="mt-1 text-xs text-gray-400">{{ $ingredientCount }} ingredients tracked</p>
            </div>
            <div class="rounded-3xl bg-brand p-6 text-white">
                <p class="text-sm text-orange-100">Today's sales</p>
                <p class="num mt-2 text-3xl font-bold tracking-tight">৳{{ number_format($todaySales) }}</p>
                <p class="mt-1 text-xs text-orange-100">{{ $todayOrders }} orders today</p>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow-sm">
                <p class="text-sm text-gray-500">This week</p>
                <p class="num mt-2 text-3xl font-bold tracking-tight">৳{{ number_format($thisWeek) }}</p>
                @if (!is_null($weekDelta))
                    <p class="mt-1 text-xs font-semibold {{ $weekDelta >= 0 ? 'text-green-600' : 'text-brand' }}">
                        {{ $weekDelta >= 0 ? '↑' : '↓' }} {{ abs($weekDelta) }}% vs last week
                    </p>
                @else
                    <p class="mt-1 text-xs text-gray-400">no data last week</p>
                @endif
            </div>
        </div>

        {{-- Sales chart --}}
        <div class="rounded-3xl bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold">Sales — last 14 days</h2>
                <a href="{{ route('sales.index') }}" class="text-sm font-medium text-gray-400 hover:text-ink">All sales →</a>
            </div>
            <div class="h-60"><canvas id="salesChart" role="img" aria-label="Bar chart of daily sales totals for the last 14 days"></canvas></div>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            {{-- Top sellers --}}
            <div class="rounded-3xl bg-white p-6 shadow-sm">
                <h2 class="mb-4 font-semibold">Top sellers <span class="text-sm font-normal text-gray-400">(7 days)</span></h2>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-[11px] font-medium uppercase tracking-wide text-gray-400">
                            <th class="pb-2">Item</th><th class="pb-2 text-right">Sold</th><th class="pb-2 text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topSellers as $row)
                            <tr class="border-t border-gray-100">
                                <td class="py-2.5 font-medium">{{ $row->menuItem->name }}</td>
                                <td class="num py-2.5 text-right">{{ $row->sold }}</td>
                                <td class="num py-2.5 text-right">৳{{ number_format($row->revenue) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-6 text-center text-gray-400">No sales yet — record one from the POS.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Low stock --}}
            <div class="rounded-3xl bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="flex items-center gap-2 font-semibold">Low stock
                        @if ($low->count())<span class="rounded-full bg-brand/10 px-2 py-0.5 text-xs font-bold text-brand">{{ $low->count() }}</span>@endif
                    </h2>
                    <a href="{{ route('purchases.index') }}" class="text-sm font-medium text-gray-400 hover:text-ink">Restock →</a>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-[11px] font-medium uppercase tracking-wide text-gray-400">
                            <th class="pb-2">Ingredient</th><th class="pb-2 text-right">Stock</th><th class="pb-2 text-right">Reorder at</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($low->take(6) as $ingredient)
                            <tr class="border-t border-gray-100">
                                <td class="flex items-center gap-2 py-2.5 font-medium"><x-icon name="alert" class="h-4 w-4 shrink-0 text-brand"/>{{ $ingredient->name }}</td>
                                <td class="num py-2.5 text-right font-semibold text-brand">{{ $ingredient->stock + 0 }} {{ $ingredient->unit }}</td>
                                <td class="num py-2.5 text-right text-gray-500">{{ $ingredient->reorder_level + 0 }} {{ $ingredient->unit }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-6 text-center text-gray-400">All ingredients above reorder level.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right column --}}
    <div class="space-y-5">
        <div class="rounded-3xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 font-semibold">Stock value by category</h2>
            @if ($byCategory->count())
                <div class="mx-auto h-44 w-44"><canvas id="categoryChart" role="img" aria-label="Donut chart of stock value by ingredient category"></canvas></div>
                <ul class="mt-4 space-y-1.5 text-sm">
                    @foreach ($byCategory as $i => $cat)
                        <li class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full" style="background: {{ ['#191919','#F4470B','#9CA3AF','#FDBA74','#4B5563','#D1D5DB','#78716C','#FCA5A5'][$i % 8] }}"></span>
                            <span class="text-gray-600">{{ $cat->category }}</span>
                            <span class="num ms-auto font-semibold">৳{{ number_format($cat->value) }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="py-6 text-center text-sm text-gray-400">No stock yet.</p>
            @endif
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-sm">
            <h2 class="mb-4 flex items-center gap-2 font-semibold"><x-icon name="clock" class="h-4 w-4 text-amber-500"/>Expiring within 7 days</h2>
            <ul class="space-y-2.5 text-sm">
                @forelse ($expiring->take(6) as $batch)
                    <li class="flex items-center gap-2">
                        <div class="min-w-0">
                            <p class="truncate font-medium">{{ $batch->ingredient->name }}</p>
                            <p class="num text-xs text-gray-400">batch of {{ $batch->qty + 0 }} {{ $batch->ingredient->unit }}</p>
                        </div>
                        <span class="num ms-auto shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $batch->expiry_date->diffInDays(today()) >= -2 ? 'bg-brand/10 text-brand' : 'bg-amber-100 text-amber-700' }}">
                            {{ $batch->expiry_date->format('d M') }}
                        </span>
                    </li>
                @empty
                    <li class="py-4 text-center text-gray-400">Nothing expiring soon.</li>
                @endforelse
            </ul>
        </div>

        <a href="{{ route('reports.index') }}"
           class="flex items-center justify-center gap-2 rounded-3xl bg-ink px-6 py-4 text-sm font-semibold text-white transition hover:bg-black">
            <x-icon name="download" class="h-4 w-4"/> Export statistics
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    const sales = @json($chart);
    const max = Math.max(...sales.data);
    new Chart(document.getElementById('salesChart'), {
        type: 'bar',
        data: { labels: sales.labels, datasets: [{
            data: sales.data,
            backgroundColor: sales.data.map(v => v === max && max > 0 ? '#F4470B' : '#191919'),
            borderRadius: 6, barThickness: 'flex', maxBarThickness: 18,
        }]},
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => '৳' + c.parsed.y.toLocaleString() } } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 } } },
                y: { grid: { color: '#F3F4F6' }, ticks: { color: '#9CA3AF', font: { size: 11 }, callback: v => '৳' + v.toLocaleString() } },
            },
        },
    });

    @if ($byCategory->count())
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: @json($byCategory->pluck('category')),
            datasets: [{
                data: @json($byCategory->pluck('value')->map(fn ($v) => round($v))),
                backgroundColor: ['#191919','#F4470B','#9CA3AF','#FDBA74','#4B5563','#D1D5DB','#78716C','#FCA5A5'],
                borderWidth: 2, borderColor: '#fff',
            }],
        },
        options: {
            maintainAspectRatio: false, cutout: '68%',
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ৳' + c.parsed.toLocaleString() } } },
        },
    });
    @endif
</script>
@endpush
