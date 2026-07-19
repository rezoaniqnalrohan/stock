@extends('layouts.app')
@section('title', 'Inventory')

@section('content')
<x-page-header title="Inventory" subtitle="Per-outlet stock levels and adjustments" />

<x-card class="mb-5" pad="p-3">
    <form method="GET" class="flex flex-wrap items-center gap-2">
        <input name="search" value="{{ request('search') }}" placeholder="Search product…"
               class="flex-1 min-w-[180px] rounded-xl border border-line bg-white px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-accent/40">
        <select name="outlet" onchange="this.form.submit()" class="rounded-xl border border-line bg-white px-3 py-2 text-sm">
            <option value="all" @selected($outletId === 'all')>All outlets</option>
            @foreach ($outlets as $o)
                <option value="{{ $o->id }}" @selected((string) $outletId === (string) $o->id)>{{ $o->name }}</option>
            @endforeach
        </select>
        <label class="flex items-center gap-2 text-sm text-muted px-2">
            <input type="checkbox" name="low" value="1" onchange="this.form.submit()" @checked($onlyLow) class="rounded border-line text-accent focus:ring-accent"> Low stock only
        </label>
        <button class="rounded-xl bg-accent text-sidebar font-semibold px-4 py-2 text-sm">Filter</button>
    </form>
</x-card>

<div class="grid lg:grid-cols-3 gap-5">
    {{-- Stock table --}}
    <x-card class="lg:col-span-2 overflow-hidden" pad="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-muted border-b border-line">
                        <th class="font-medium px-5 py-3">Product</th>
                        <th class="font-medium px-5 py-3">Outlet</th>
                        <th class="font-medium px-5 py-3 text-right">On hand</th>
                        <th class="font-medium px-5 py-3 text-right">Reorder</th>
                        <th class="font-medium px-5 py-3 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($stocks as $s)
                        <tr class="hover:bg-cream/60">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-lg">{{ $s->product->image_url ?? '📦' }}</span>
                                    <div>
                                        <p class="font-medium leading-tight">{{ $s->product->name }}</p>
                                        <p class="text-xs text-muted">{{ $s->product->variant }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-muted">{{ $s->outlet->name }}</td>
                            <td class="px-5 py-3 text-right font-semibold tabular-nums">{{ $s->quantity }}</td>
                            <td class="px-5 py-3 text-right text-muted tabular-nums">{{ $s->product->reorder_point }}</td>
                            <td class="px-5 py-3 text-right">
                                @if ($s->quantity <= 0)
                                    <span class="text-xs font-semibold px-2 py-1 rounded-lg bg-red-50 text-red-600">Out</span>
                                @elseif ($s->quantity <= $s->product->reorder_point)
                                    <span class="text-xs font-semibold px-2 py-1 rounded-lg bg-amber-50 text-amber-600">Low</span>
                                @else
                                    <span class="text-xs font-semibold px-2 py-1 rounded-lg bg-accent/15 text-accentdk">OK</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-10">No stock rows match.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $stocks->links() }}</div>
    </x-card>

    {{-- Adjust + history --}}
    <div class="space-y-5">
        <x-card>
            <h3 class="font-display text-lg font-bold mb-3">Adjust stock</h3>
            <form method="POST" action="{{ route('inventory.adjust') }}" class="space-y-3">
                @csrf
                <select name="product_id" required class="w-full rounded-xl border border-line px-3 py-2.5 text-sm">
                    <option value="">Select product…</option>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} — {{ $p->variant }}</option>
                    @endforeach
                </select>
                <select name="outlet_id" required class="w-full rounded-xl border border-line px-3 py-2.5 text-sm">
                    <option value="">Select outlet…</option>
                    @foreach ($outlets as $o)
                        <option value="{{ $o->id }}">{{ $o->name }}</option>
                    @endforeach
                </select>
                <input name="delta" type="number" required placeholder="Change (e.g. -2 or 5)"
                       class="w-full rounded-xl border border-line px-3.5 py-2.5 text-sm">
                <input name="reason" placeholder="Reason (optional)"
                       class="w-full rounded-xl border border-line px-3.5 py-2.5 text-sm">
                <button class="w-full rounded-xl bg-sidebar text-white text-sm font-semibold py-2.5 hover:bg-black transition">Apply adjustment</button>
            </form>
        </x-card>

        <x-card>
            <h3 class="font-display text-lg font-bold mb-3">Recent adjustments</h3>
            <div class="space-y-3">
                @forelse ($adjustments as $a)
                    <div class="flex items-center justify-between text-sm">
                        <div class="min-w-0">
                            <p class="font-medium truncate">{{ $a->product->name }}</p>
                            <p class="text-xs text-muted truncate">{{ $a->outlet->name }} · {{ $a->reason ?: '—' }}</p>
                        </div>
                        <span class="font-semibold tabular-nums {{ $a->delta < 0 ? 'text-red-600' : 'text-accentdk' }}">{{ $a->delta > 0 ? '+' : '' }}{{ $a->delta }}</span>
                    </div>
                @empty
                    <p class="text-sm text-muted">No adjustments yet.</p>
                @endforelse
            </div>
        </x-card>
    </div>
</div>
@endsection
