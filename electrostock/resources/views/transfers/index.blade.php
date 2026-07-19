@extends('layouts.app')
@section('title', 'Transfers')

@section('content')
<x-page-header title="Stock Transfers" subtitle="Move inventory between outlets and warehouses" />

<div class="grid lg:grid-cols-3 gap-5">
    <x-card class="lg:col-span-2 overflow-hidden" pad="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-muted border-b border-line">
                        <th class="font-medium px-5 py-3">Product</th>
                        <th class="font-medium px-5 py-3">Route</th>
                        <th class="font-medium px-5 py-3 text-right">Qty</th>
                        <th class="font-medium px-5 py-3">Status</th>
                        <th class="font-medium px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($transfers as $t)
                        <tr class="hover:bg-cream/60">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-lg">{{ $t->product->image_url ?? '📦' }}</span>
                                    <span class="font-medium">{{ $t->product->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-muted">{{ $t->fromOutlet->name }} → {{ $t->toOutlet->name }}</td>
                            <td class="px-5 py-3 text-right font-semibold tabular-nums">{{ $t->quantity }}</td>
                            <td class="px-5 py-3">
                                @if ($t->status === 'pending')
                                    <span class="text-xs font-semibold px-2 py-1 rounded-lg bg-amber-50 text-amber-600">Pending</span>
                                @else
                                    <span class="text-xs font-semibold px-2 py-1 rounded-lg bg-accent/15 text-accentdk">Received</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                @if ($t->status === 'pending')
                                    <form method="POST" action="{{ route('transfers.receive', $t) }}">
                                        @csrf
                                        <button class="text-sm font-semibold text-accentdk hover:underline">Receive</button>
                                    </form>
                                @else
                                    <span class="text-xs text-muted">{{ $t->received_at?->diffForHumans() }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-10">No transfers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $transfers->links() }}</div>
    </x-card>

    <x-card>
        <h3 class="font-display text-lg font-bold mb-3">New transfer</h3>
        <form method="POST" action="{{ route('transfers.store') }}" class="space-y-3">
            @csrf
            <select name="product_id" required class="w-full rounded-xl border border-line px-3 py-2.5 text-sm">
                <option value="">Select product…</option>
                @foreach ($products as $p)
                    <option value="{{ $p->id }}">{{ $p->name }} — {{ $p->variant }}</option>
                @endforeach
            </select>
            <select name="from_outlet_id" required class="w-full rounded-xl border border-line px-3 py-2.5 text-sm">
                <option value="">From outlet…</option>
                @foreach ($outlets as $o)
                    <option value="{{ $o->id }}">{{ $o->name }}</option>
                @endforeach
            </select>
            <select name="to_outlet_id" required class="w-full rounded-xl border border-line px-3 py-2.5 text-sm">
                <option value="">To outlet…</option>
                @foreach ($outlets as $o)
                    <option value="{{ $o->id }}">{{ $o->name }}</option>
                @endforeach
            </select>
            <input name="quantity" type="number" min="1" required placeholder="Quantity"
                   class="w-full rounded-xl border border-line px-3.5 py-2.5 text-sm">
            <button class="w-full rounded-xl bg-sidebar text-white text-sm font-semibold py-2.5 hover:bg-black transition">Create transfer</button>
            <p class="text-xs text-muted">Stock leaves the source now and lands when the destination confirms receipt.</p>
        </form>
    </x-card>
</div>
@endsection
