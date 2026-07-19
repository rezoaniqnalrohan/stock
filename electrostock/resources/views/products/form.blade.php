@extends('layouts.app')
@section('title', $product->exists ? 'Edit product' : 'New product')

@section('content')
@php $editing = $product->exists; @endphp
<x-page-header :title="$editing ? 'Edit product' : 'New product'" subtitle="Consumer electronics catalog item">
    <x-slot:actions>
        <a href="{{ route('products.index') }}" class="rounded-xl border border-line text-sm font-medium px-4 py-2.5 hover:bg-white">Back</a>
    </x-slot:actions>
</x-page-header>

<x-card class="max-w-3xl">
    <form method="POST" action="{{ $editing ? route('products.update', $product) : route('products.store') }}" class="grid sm:grid-cols-2 gap-4">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="sm:col-span-2">
            <label class="block text-sm font-medium mb-1.5">Product name</label>
            <input name="name" value="{{ old('name', $product->name) }}" required
                   class="w-full rounded-xl border border-line px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-accent/40">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5">SKU</label>
            <input name="sku" value="{{ old('sku', $product->sku ?? 'ELS-') }}" required
                   class="w-full rounded-xl border border-line px-3.5 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-accent/40">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1.5">Barcode</label>
            <input name="barcode" value="{{ old('barcode', $product->barcode) }}"
                   class="w-full rounded-xl border border-line px-3.5 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-accent/40">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5">Brand</label>
            <select name="brand_id" class="w-full rounded-xl border border-line px-3 py-2.5 text-sm">
                @foreach ($brands as $b)
                    <option value="{{ $b->id }}" @selected(old('brand_id', $product->brand_id) == $b->id)>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1.5">Category</label>
            <select name="category_id" class="w-full rounded-xl border border-line px-3 py-2.5 text-sm">
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5">Variant (color / storage)</label>
            <input name="variant" value="{{ old('variant', $product->variant) }}" placeholder="Black / 256GB"
                   class="w-full rounded-xl border border-line px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-accent/40">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1.5">Thumbnail (emoji or URL)</label>
            <input name="image_url" value="{{ old('image_url', $product->image_url ?? '📦') }}"
                   class="w-full rounded-xl border border-line px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-accent/40">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5">Retail price ($)</label>
            <input name="price" type="number" step="0.01" value="{{ old('price', $product->price ?? 0) }}" required
                   class="w-full rounded-xl border border-line px-3.5 py-2.5 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1.5">Unit cost ($)</label>
            <input name="cost" type="number" step="0.01" value="{{ old('cost', $product->cost ?? 0) }}" required
                   class="w-full rounded-xl border border-line px-3.5 py-2.5 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5">Reorder point</label>
            <input name="reorder_point" type="number" value="{{ old('reorder_point', $product->reorder_point ?? 5) }}" required
                   class="w-full rounded-xl border border-line px-3.5 py-2.5 text-sm">
        </div>

        <div class="sm:col-span-2 flex gap-2 pt-2">
            <button class="rounded-xl bg-sidebar text-white text-sm font-semibold px-5 py-2.5 hover:bg-black transition">{{ $editing ? 'Save changes' : 'Create product' }}</button>
        </div>
    </form>
</x-card>
@endsection
