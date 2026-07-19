@extends('layouts.app')
@section('title', 'Reporting')

@section('content')
<x-page-header title="Reporting" subtitle="Sales, inventory and movement across the chain" />

<div class="grid lg:grid-cols-2 gap-5">
    {{-- Sales by outlet --}}
    <x-card>
        <h3 class="font-display text-lg font-bold mb-4">Sales by Outlet</h3>
        <div class="space-y-3">
            @php $max = max(1, $salesByOutlet->max('revenue') ?? 1); @endphp
            @foreach ($salesByOutlet as $o)
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="font-medium">{{ $o->name }}</span>
                        <span class="font-semibold tabular-nums">${{ number_format($o->revenue ?? 0, 2) }} <span class="text-muted font-normal">· {{ $o->sales_count }} sales</span></span>
                    </div>
                    <div class="h-2 rounded-full bg-cream overflow-hidden">
                        <div class="h-full bg-olive rounded-full" style="width: {{ round(($o->revenue ?? 0) / $max * 100) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-card>

    {{-- Best sellers --}}
    <x-card>
        <h3 class="font-display text-lg font-bold mb-4">Best Sellers</h3>
        <div class="space-y-2.5">
            @forelse ($bestSellers as $b)
                <div class="flex items-center gap-3">
                    <span class="text-lg">{{ $b->product->image_url ?? '📦' }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ $b->product->name }}</p>
                    </div>
                    <span class="text-sm font-semibold tabular-nums">{{ $b->units }} sold</span>
                    <span class="text-sm text-muted tabular-nums w-20 text-right">${{ number_format($b->revenue, 0) }}</span>
                </div>
            @empty
                <p class="text-sm text-muted">No sales recorded.</p>
            @endforelse
        </div>
    </x-card>

    {{-- Stock on hand --}}
    <x-card class="overflow-hidden" pad="p-0">
        <h3 class="font-display text-lg font-bold px-5 pt-5 mb-3">Stock on Hand & Valuation</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-muted border-y border-line">
                        <th class="font-medium px-5 py-2.5">Outlet</th>
                        <th class="font-medium px-5 py-2.5 text-right">Units</th>
                        <th class="font-medium px-5 py-2.5 text-right">Cost value</th>
                        <th class="font-medium px-5 py-2.5 text-right">Retail value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($stockOnHand as $s)
                        <tr>
                            <td class="px-5 py-2.5 font-medium">{{ $s->outlet }}</td>
                            <td class="px-5 py-2.5 text-right tabular-nums">{{ (int) $s->units }}</td>
                            <td class="px-5 py-2.5 text-right tabular-nums text-muted">${{ number_format($s->cost_value, 0) }}</td>
                            <td class="px-5 py-2.5 text-right tabular-nums font-semibold">${{ number_format($s->retail_value, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Recent transfers --}}
    <x-card>
        <h3 class="font-display text-lg font-bold mb-4">Recent Transfers</h3>
        <div class="space-y-2.5">
            @forelse ($transfers as $t)
                <div class="flex items-center justify-between text-sm">
                    <div class="min-w-0">
                        <p class="font-medium truncate">{{ $t->product->name }} <span class="text-muted font-normal">×{{ $t->quantity }}</span></p>
                        <p class="text-xs text-muted truncate">{{ $t->fromOutlet->name }} → {{ $t->toOutlet->name }}</p>
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 rounded-lg {{ $t->status === 'pending' ? 'bg-amber-50 text-amber-600' : 'bg-accent/15 text-accentdk' }} capitalize">{{ $t->status }}</span>
                </div>
            @empty
                <p class="text-sm text-muted">No transfers.</p>
            @endforelse
        </div>
    </x-card>
</div>
@endsection
