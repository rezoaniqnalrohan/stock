@extends('layouts.app')
@section('title', 'Purchase Orders')

@section('content')
<x-page-header title="Purchase Orders" subtitle="Restock from your suppliers">
    <x-slot:actions>
        <a href="{{ route('purchase-orders.create') }}" class="rounded-xl bg-sidebar text-white text-sm font-semibold px-4 py-2.5 hover:bg-black transition">+ New PO</a>
    </x-slot:actions>
</x-page-header>

<div class="space-y-4">
    @forelse ($orders as $po)
        <x-card>
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-display font-bold">{{ $po->reference }}</span>
                        @php $c = ['draft' => 'bg-gray-100 text-gray-600', 'dispatched' => 'bg-amber-50 text-amber-600', 'received' => 'bg-accent/15 text-accentdk'][$po->status]; @endphp
                        <span class="text-xs font-semibold px-2 py-1 rounded-lg {{ $c }} capitalize">{{ $po->status }}</span>
                    </div>
                    <p class="text-sm text-muted mt-1">{{ $po->supplier->name }} → {{ $po->outlet->name }} · {{ $po->created_at->format('j M Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="font-display text-xl font-bold">${{ number_format($po->total, 2) }}</p>
                    <p class="text-xs text-muted">{{ $po->items->count() }} line items</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 mt-4">
                @foreach ($po->items as $item)
                    <div class="flex items-center gap-2 bg-cream rounded-xl px-3 py-1.5 text-sm">
                        <span>{{ $item->product->image_url ?? '📦' }}</span>
                        <span class="font-medium">{{ $item->product->name }}</span>
                        <span class="text-muted">×{{ $item->quantity }}</span>
                    </div>
                @endforeach
            </div>

            <div class="flex gap-2 mt-4">
                @if ($po->status === 'draft')
                    <form method="POST" action="{{ route('purchase-orders.dispatch', $po) }}">
                        @csrf
                        <button class="rounded-lg border border-line text-sm font-medium px-4 py-2 hover:bg-cream">Mark dispatched</button>
                    </form>
                @endif
                @if ($po->status !== 'received')
                    <form method="POST" action="{{ route('purchase-orders.receive', $po) }}">
                        @csrf
                        <button class="rounded-lg bg-accent text-sidebar text-sm font-semibold px-4 py-2 hover:brightness-95">Receive into stock</button>
                    </form>
                @else
                    <span class="text-xs text-muted self-center">Received {{ $po->received_at?->diffForHumans() }}</span>
                @endif
            </div>
        </x-card>
    @empty
        <x-card class="text-center text-muted py-12">No purchase orders yet.</x-card>
    @endforelse
</div>

<div class="mt-6">{{ $orders->links() }}</div>
@endsection
