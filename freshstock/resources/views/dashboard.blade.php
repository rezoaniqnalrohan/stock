@extends('layouts.app')
@section('title', 'Dashboard')

@section('actions')
    <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-xl text-sm text-slate-600 shadow-sm cursor-pointer">
        Monthly
        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M19 9l-7 7-7-7"/></svg>
    </div>
    <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-medium shadow-sm shadow-brand-200 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export
    </button>
@endsection

@section('content')
@php
    $kpis = [
        ['label' => 'Total SKUs', 'value' => number_format($totalSkus), 'delta' => '+6 new this quarter', 'up' => true, 'from' => 'from-orange-400', 'to' => 'to-orange-500',
         'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['label' => 'Stock Value', 'value' => '$'.number_format($stockValue, 0), 'delta' => '8.2% more than last quarter', 'up' => true, 'from' => 'from-rose-400', 'to' => 'to-rose-500',
         'icon' => 'M12 8c-1.7 0-3 .9-3 2s1.3 2 3 2 3 .9 3 2-1.3 2-3 2m0-8c1.1 0 2.1.4 2.6 1M12 8V6m0 10v2m0-2c-1.1 0-2.1-.4-2.6-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Low-stock Items', 'value' => number_format($lowStock), 'delta' => 'Needs reordering', 'up' => false, 'from' => 'from-brand-400', 'to' => 'to-brand-600',
         'icon' => 'M12 9v2m0 4h.01M5 19h14a2 2 0 001.8-2.9l-7-12a2 2 0 00-3.5 0l-7 12A2 2 0 005 19z'],
        ['label' => 'Open Purchase Orders', 'value' => number_format($openPos), 'delta' => 'Awaiting delivery', 'up' => true, 'from' => 'from-sky-400', 'to' => 'to-sky-500',
         'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
    ];
@endphp

{{-- KPI cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    @foreach ($kpis as $k)
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br {{ $k['from'] }} {{ $k['to'] }} grid place-items-center text-white mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $k['icon'] }}"/></svg>
            </div>
            <p class="text-2xl font-extrabold text-slate-900">{{ $k['value'] }}</p>
            <p class="text-sm text-slate-500">{{ $k['label'] }}</p>
            <p class="mt-3 flex items-center gap-1 text-xs font-medium {{ $k['up'] ? 'text-emerald-600' : 'text-amber-600' }}">
                @if ($k['up'])
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                @else
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.8-2.9l-7-12a2 2 0 00-3.5 0l-7 12A2 2 0 005 19z"/></svg>
                @endif
                {{ $k['delta'] }}
            </p>
        </div>
    @endforeach
</div>

{{-- Charts row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
    <x-card title="Stock by category" subtitle="Units on hand" class="lg:col-span-1">
        <div class="relative h-64"><canvas id="donut"></canvas></div>
    </x-card>

    <x-card title="Stock movement" subtitle="Units in vs out, last 6 months" class="lg:col-span-1">
        <div class="h-64"><canvas id="bars"></canvas></div>
    </x-card>

    <x-card class="lg:col-span-1">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="font-bold text-slate-900">Inventory value</h3>
                <p class="text-2xl font-extrabold text-slate-900 mt-1">${{ number_format($stockValue, 0) }}</p>
                <p class="text-xs text-emerald-600 font-medium mt-0.5">Trending up this period</p>
            </div>
        </div>
        <div class="h-44 mt-2"><canvas id="area"></canvas></div>
    </x-card>
</div>

{{-- Lower tables --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
    <x-card title="Recent stock movements" class="lg:col-span-2">
        <x-slot:action>
            <a href="/reports/movement" class="text-sm text-brand-600 font-medium hover:underline">View all</a>
        </x-slot:action>
        <div class="overflow-x-auto -mx-1">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-400 uppercase tracking-wide">
                        <th class="py-2 px-1 font-semibold">Product</th>
                        <th class="py-2 px-1 font-semibold">Warehouse</th>
                        <th class="py-2 px-1 font-semibold">Type</th>
                        <th class="py-2 px-1 font-semibold text-right">Qty</th>
                        <th class="py-2 px-1 font-semibold text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($recentMovements as $m)
                        <tr>
                            <td class="py-2.5 px-1 font-medium text-slate-800">{{ $m->product->name }}</td>
                            <td class="py-2.5 px-1 text-slate-500">{{ $m->warehouse->code }}</td>
                            <td class="py-2.5 px-1">
                                <x-badge :color="$m->type === 'in' ? 'green' : ($m->type === 'out' ? 'blue' : 'amber')">{{ ucfirst($m->type) }}</x-badge>
                            </td>
                            <td class="py-2.5 px-1 text-right font-semibold tabular-nums {{ $m->quantity < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $m->quantity > 0 ? '+' : '' }}{{ number_format($m->quantity) }}
                            </td>
                            <td class="py-2.5 px-1 text-right text-slate-400 tabular-nums">{{ $m->created_at->format('M d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

    <x-card title="Expiring soon" subtitle="Next 14 days" class="lg:col-span-1">
        <x-slot:action>
            <a href="/inventory/expiring" class="text-sm text-brand-600 font-medium hover:underline">All</a>
        </x-slot:action>
        @forelse ($expiringSoon as $b)
            @php $days = (int) round(now()->startOfDay()->diffInDays($b->expiry_date, false)); @endphp
            <div class="flex items-center justify-between py-2.5 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-800 truncate">{{ $b->product->name }}</p>
                    <p class="text-xs text-slate-400">{{ $b->warehouse->code }} · {{ $b->quantity }} units</p>
                </div>
                <x-badge :color="$days <= 3 ? 'red' : 'amber'">
                    {{ $days < 0 ? 'Expired' : $days.'d' }}
                </x-badge>
            </div>
        @empty
            <x-empty message="No stock expiring soon." />
        @endforelse
    </x-card>
</div>

<script>
    const brand = '#7c3aed';
    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.color = '#94a3b8';

    new Chart(document.getElementById('donut'), {
        type: 'doughnut',
        data: {
            labels: @json($byCategory->pluck('name')),
            datasets: [{ data: @json($byCategory->pluck('qty')), backgroundColor: @json($byCategory->pluck('color')), borderWidth: 4, borderColor: '#fff', hoverOffset: 6 }]
        },
        options: { cutout: '62%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, padding: 12, font: { size: 11 } } } } }
    });

    new Chart(document.getElementById('bars'), {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [
                { label: 'Stock in', data: @json($movementIn), backgroundColor: brand, borderRadius: 6, stack: 's' },
                { label: 'Stock out', data: @json($movementOut), backgroundColor: '#c4b5fd', borderRadius: 6, stack: 's' }
            ]
        },
        options: {
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, padding: 12, font: { size: 11 } } } },
            scales: { x: { stacked: true, grid: { display: false } }, y: { stacked: true, grid: { color: '#f1f5f9' }, border: { display: false } } }
        }
    });

    const ctx = document.getElementById('area').getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 180);
    grad.addColorStop(0, 'rgba(124,58,237,0.35)');
    grad.addColorStop(1, 'rgba(124,58,237,0)');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{ data: @json($valueTrend), borderColor: brand, backgroundColor: grad, fill: true, tension: 0.4, pointRadius: 0, pointHoverRadius: 5, borderWidth: 2 }]
        },
        options: {
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => '$' + c.parsed.y.toLocaleString() } } },
            scales: { x: { grid: { display: false } }, y: { grid: { color: '#f1f5f9' }, border: { display: false }, ticks: { callback: v => '$' + (v/1000) + 'k' } } }
        }
    });
</script>
@endsection
