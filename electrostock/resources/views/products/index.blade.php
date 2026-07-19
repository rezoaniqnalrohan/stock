@extends('layouts.app')
@section('title', 'Catalog')

@section('content')
<x-page-header title="Catalog" subtitle="{{ $products->total() }} products across your chain">
    <x-slot:actions>
        <a href="{{ route('products.create') }}" class="rounded-xl bg-sidebar text-white text-sm font-semibold px-4 py-2.5 hover:bg-black transition">+ New product</a>
    </x-slot:actions>
</x-page-header>

<x-card class="mb-5" pad="p-3">
    <form method="GET" class="flex flex-wrap gap-2">
        <input name="search" value="{{ request('search') }}" placeholder="Search name or SKU…"
               class="flex-1 min-w-[200px] rounded-xl border border-line bg-white px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-accent/40">
        <select name="category" onchange="this.form.submit()" class="rounded-xl border border-line bg-white px-3 py-2 text-sm">
            <option value="">All categories</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <button class="rounded-xl bg-accent text-sidebar font-semibold px-4 py-2 text-sm">Search</button>
    </form>
</x-card>

<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse ($products as $p)
        <x-card class="flex flex-col">
            <div class="flex items-start gap-3">
                <div class="w-14 h-14 rounded-xl bg-cream grid place-items-center text-2xl shrink-0">{{ $p->image_url ?? '📦' }}</div>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold leading-tight truncate">{{ $p->name }}</p>
                    <p class="text-xs text-muted mt-0.5">{{ $p->brand->name }} · {{ $p->category->name }}</p>
                    <p class="text-xs text-muted font-mono mt-0.5">{{ $p->sku }}</p>
                </div>
            </div>
            @if ($p->variant)
                <p class="text-xs text-muted mt-3">{{ $p->variant }}</p>
            @endif
            <div class="flex items-end justify-between mt-3 pt-3 border-t border-line">
                <div>
                    <p class="font-display text-lg font-bold">${{ number_format($p->price, 2) }}</p>
                    <p class="text-xs text-muted">cost ${{ number_format($p->cost, 2) }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-block text-xs font-semibold px-2 py-1 rounded-lg {{ ($p->total_stock ?? 0) <= $p->reorder_point ? 'bg-red-50 text-red-600' : 'bg-accent/15 text-accentdk' }}">
                        {{ (int) ($p->total_stock ?? 0) }} in stock
                    </span>
                </div>
            </div>
            <div class="flex gap-2 mt-3">
                <a href="{{ route('products.edit', $p) }}" class="flex-1 text-center rounded-lg border border-line text-sm font-medium py-1.5 hover:bg-cream">Edit</a>
                <form method="POST" action="{{ route('products.destroy', $p) }}" onsubmit="return confirm('Delete this product?')">
                    @csrf @method('DELETE')
                    <button class="rounded-lg border border-line text-red-600 text-sm font-medium px-3 py-1.5 hover:bg-red-50">Delete</button>
                </form>
            </div>
        </x-card>
    @empty
        <p class="text-muted col-span-full text-center py-12">No products found.</p>
    @endforelse
</div>

<div class="mt-6">{{ $products->links() }}</div>
@endsection
