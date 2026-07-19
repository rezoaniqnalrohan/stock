@extends('layouts.app')
@section('title', 'Adjust stock')
@section('heading', 'Stock adjustment')
@section('subheading', 'Record a manual correction, spoilage write-off or count fix')

@section('content')
@php $field = 'w-full h-11 px-3.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none text-sm'; $label='block text-sm font-medium text-slate-700 mb-1.5'; @endphp
<form method="POST" action="/inventory/adjust" class="max-w-xl">
    @csrf
    <x-card class="space-y-5">
        <div>
            <label class="{{ $label }}">Product</label>
            <select name="product_id" class="{{ $field }}" required>
                @foreach ($products as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>@endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">Warehouse</label>
            <select name="warehouse_id" class="{{ $field }}" required>
                @foreach ($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">Quantity change</label>
            <input name="quantity" type="number" value="{{ old('quantity') }}" placeholder="e.g. -12 for spoilage, 30 for a count-up" class="{{ $field }}" required>
            <p class="text-xs text-slate-400 mt-1.5">Use a negative number to remove stock, positive to add.</p>
        </div>
        <div>
            <label class="{{ $label }}">Note</label>
            <input name="note" value="{{ old('note') }}" placeholder="Reason for adjustment" class="{{ $field }}">
        </div>
        <div class="flex items-center gap-3 pt-1">
            <button class="h-11 px-6 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold shadow-sm shadow-brand-200">Record adjustment</button>
            <a href="/inventory" class="h-11 px-5 grid place-items-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium">Cancel</a>
        </div>
    </x-card>
</form>
@endsection
