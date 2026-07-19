@extends('layouts.app')
@section('title', 'Home')

@section('content')
@php
    $delta = $totalSold - $prevTotal;
    $rangeLabel = ['today' => 'today', 'week' => 'this week', 'month' => 'this month'][$range];
    $ranges = ['today' => 'Today', 'week' => 'This Week', 'month' => 'This Month'];
@endphp

{{-- Greeting header --}}
<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <h1 class="font-display text-3xl sm:text-[34px] leading-tight font-bold tracking-tight max-w-md">
        Hi {{ explode(' ', auth()->user()->name)[0] }}, here's what's<br class="hidden sm:block"> happening in your stores
    </h1>
    <div class="flex flex-wrap items-center gap-2">
        <div class="inline-flex bg-white rounded-xl border border-line p-1">
            @foreach ($ranges as $key => $label)
                <a href="{{ route('dashboard', ['range' => $key, 'outlet' => $outletId]) }}"
                   class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition {{ $range === $key ? 'bg-sidebar text-white' : 'text-muted hover:text-ink' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
        <form method="GET" action="{{ route('dashboard') }}">
            <input type="hidden" name="range" value="{{ $range }}">
            <select name="outlet" onchange="this.form.submit()"
                    class="rounded-xl border border-line bg-white px-3.5 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-accent/40">
                <option value="all" @selected($outletId === 'all')>All Outlets</option>
                @foreach ($outlets as $o)
                    <option value="{{ $o->id }}" @selected((string) $outletId === (string) $o->id)>{{ $o->name }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- Top KPI band --}}
<x-card class="mb-5">
    <div class="grid lg:grid-cols-12 gap-6 items-center">
        <div class="lg:col-span-3">
            <p class="text-sm text-muted">Your stores have sold {{ $rangeLabel }}</p>
            <p class="font-display text-4xl font-bold mt-2">${{ number_format($totalSold, 2) }}</p>
            <p class="text-sm mt-2 {{ $delta >= 0 ? 'text-accentdk' : 'text-red-600' }}">
                {{ $delta >= 0 ? "That's $" . number_format(abs($delta), 2) . ' more' : 'That\'s $' . number_format(abs($delta), 2) . ' less' }}
                than last period.
            </p>
        </div>
        <div class="lg:col-span-6 min-w-0">
            <p class="text-sm font-medium text-muted mb-2">{{ $outletId === 'all' ? 'All Outlets' : $outlets->firstWhere('id', (int) $outletId)?->name }}</p>
            <div class="h-[190px]"><canvas id="salesChart"></canvas></div>
        </div>
        <div class="lg:col-span-3 lg:border-l lg:border-line lg:pl-6 grid grid-cols-2 lg:grid-cols-1 gap-5">
            <div>
                <p class="text-sm text-muted">Average Sale Value</p>
                <p class="font-display text-3xl font-bold mt-1">${{ number_format($avgSale, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-muted">Average Items per Sale</p>
                <p class="font-display text-3xl font-bold mt-1">{{ number_format($avgItems, 1) }}</p>
            </div>
        </div>
    </div>
</x-card>

{{-- Secondary row --}}
<div class="grid lg:grid-cols-3 gap-5">
    {{-- Sales chart + retail metrics --}}
    <x-card class="lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display text-lg font-bold">Your Sales {{ $rangeLabel === 'this month' ? 'this Month' : ucfirst($rangeLabel) }}</h2>
            <a href="{{ route('reports') }}" class="text-xs font-semibold text-accentdk hover:underline">+ SHOW MORE RETAIL METRICS</a>
        </div>
        <div class="h-[210px]"><canvas id="salesChart2"></canvas></div>
        <div class="grid grid-cols-3 gap-4 mt-5 pt-5 border-t border-line">
            <div>
                <p class="text-xs text-muted">Total Sales</p>
                <p class="font-display text-xl font-bold mt-0.5">${{ number_format($totalSold, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-muted">Transactions</p>
                <p class="font-display text-xl font-bold mt-0.5">{{ $count }}</p>
            </div>
            <div>
                <p class="text-xs text-muted">Low-stock Alerts</p>
                <p class="font-display text-xl font-bold mt-0.5 {{ $lowStock ? 'text-red-600' : '' }}">{{ $lowStock }}</p>
            </div>
        </div>
    </x-card>

    <div class="space-y-5">
        {{-- Transfers --}}
        <x-card>
            <div class="flex items-center justify-between mb-1">
                <h3 class="font-display text-lg font-bold">Transfers</h3>
                <a href="{{ route('transfers.index') }}" class="text-xs font-semibold text-accentdk hover:underline">VIEW TRANSFERS</a>
            </div>
            <p class="text-sm text-muted mb-4">You have {{ $pendingTransfers->count() }} transfer{{ $pendingTransfers->count() === 1 ? '' : 's' }} waiting to be received</p>
            <div class="space-y-3">
                @forelse ($pendingTransfers->take(3) as $t)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-cream grid place-items-center text-xl shrink-0">{{ $t->product->image_url ?? '📦' }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold truncate">{{ $t->product->name }} <span class="text-muted font-normal">· {{ $t->quantity }} pcs</span></p>
                            <p class="text-xs text-muted truncate">{{ $t->fromOutlet->name }} → {{ $t->toOutlet->name }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-muted">No pending transfers.</p>
                @endforelse
            </div>
        </x-card>

        {{-- Purchase Orders --}}
        <x-card>
            <div class="flex items-center justify-between mb-1">
                <h3 class="font-display text-lg font-bold">Purchase Orders</h3>
                <a href="{{ route('purchase-orders.index') }}" class="text-xs font-semibold text-accentdk hover:underline">VIEW ORDERS</a>
            </div>
            <p class="text-sm text-muted mb-4">You have {{ $dispatchedPOs->count() }} dispatched order{{ $dispatchedPOs->count() === 1 ? '' : 's' }} waiting to be received</p>
            <div class="flex flex-wrap gap-2">
                @php $thumbs = $dispatchedPOs->flatMap->items->take(7); @endphp
                @forelse ($thumbs as $item)
                    <div class="w-11 h-11 rounded-xl bg-cream grid place-items-center text-xl" title="{{ $item->product->name }}">{{ $item->product->image_url ?? '📦' }}</div>
                @empty
                    <p class="text-sm text-muted">No dispatched orders.</p>
                @endforelse
            </div>
        </x-card>
    </div>
</div>

@push('scripts')
<script>
    const labels = @json($labels);
    const data = @json($series);

    function oliveArea(ctx) {
        const g = ctx.createLinearGradient(0, 0, 0, 200);
        g.addColorStop(0, 'rgba(154,162,58,0.28)');
        g.addColorStop(1, 'rgba(154,162,58,0.02)');
        return g;
    }
    const baseOpts = {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => '$' + c.parsed.y.toLocaleString() } } },
        scales: {
            x: { grid: { display: false }, ticks: { maxTicksLimit: 8, color: '#9a9a8c', font: { size: 11 } } },
            y: { grid: { color: '#ECE9DD' }, ticks: { color: '#9a9a8c', font: { size: 11 }, callback: v => v >= 1000 ? (v/1000)+'k' : v } },
        },
        elements: { point: { radius: 0, hoverRadius: 5 } },
    };
    function mk(id) {
        const el = document.getElementById(id);
        if (!el) return;
        new Chart(el, {
            type: 'line',
            data: { labels, datasets: [{
                data, borderColor: '#9AA23A', borderWidth: 2,
                fill: true, backgroundColor: oliveArea(el.getContext('2d')), tension: 0.35,
            }]},
            options: baseOpts,
        });
    }
    mk('salesChart'); mk('salesChart2');
</script>
@endpush
@endsection
