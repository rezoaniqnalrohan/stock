@extends('layouts.app')
@section('title', $product->exists ? 'Edit product' : 'New product')
@section('heading', $product->exists ? 'Edit product' : 'New product')
@section('subheading', 'Catalog details, pricing and storage')

@section('content')
@php
    $field = 'w-full h-11 px-3.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none text-sm';
    $label = 'block text-sm font-medium text-slate-700 mb-1.5';
@endphp
<form method="POST" action="{{ $product->exists ? '/products/'.$product->id : '/products' }}" class="max-w-3xl">
    @csrf
    @if ($product->exists) @method('PUT') @endif

    <x-card class="space-y-5">
        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="{{ $label }}">SKU</label>
                <input name="sku" value="{{ old('sku', $product->sku) }}" class="{{ $field }}" required>
            </div>
            <div>
                <label class="{{ $label }}">Product name</label>
                <input name="name" value="{{ old('name', $product->name) }}" class="{{ $field }}" required>
            </div>
            <div>
                <label class="{{ $label }}">Category</label>
                <select name="category_id" class="{{ $field }}" required>
                    @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Unit of measure</label>
                <select name="unit_id" class="{{ $field }}" required>
                    @foreach ($units as $u)<option value="{{ $u->id }}" @selected(old('unit_id', $product->unit_id) == $u->id)>{{ $u->name }} ({{ $u->abbreviation }})</option>@endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Supplier</label>
                <select name="supplier_id" class="{{ $field }}">
                    <option value="">— none —</option>
                    @foreach ($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id', $product->supplier_id) == $s->id)>{{ $s->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Storage temperature</label>
                <select name="storage_temp" class="{{ $field }}" required>
                    @foreach (['ambient' => 'Ambient', 'chilled' => 'Chilled', 'frozen' => 'Frozen'] as $v => $t)
                        <option value="{{ $v }}" @selected(old('storage_temp', $product->storage_temp) == $v)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Sell price ($)</label>
                <input name="price" type="number" step="0.01" value="{{ old('price', $product->price ?? 0) }}" class="{{ $field }}" required>
            </div>
            <div>
                <label class="{{ $label }}">Cost ($)</label>
                <input name="cost" type="number" step="0.01" value="{{ old('cost', $product->cost ?? 0) }}" class="{{ $field }}" required>
            </div>
            <div>
                <label class="{{ $label }}">Shelf life (days)</label>
                <input name="shelf_life_days" type="number" value="{{ old('shelf_life_days', $product->shelf_life_days ?? 0) }}" class="{{ $field }}" required>
            </div>
            <div>
                <label class="{{ $label }}">Reorder point (units)</label>
                <input name="reorder_point" type="number" value="{{ old('reorder_point', $product->reorder_point ?? 0) }}" class="{{ $field }}" required>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button class="h-11 px-6 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold shadow-sm shadow-brand-200">
                {{ $product->exists ? 'Save changes' : 'Create product' }}
            </button>
            <a href="/products" class="h-11 px-5 grid place-items-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium">Cancel</a>
        </div>
    </x-card>
</form>
@endsection
