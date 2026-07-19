@extends('layouts.app')
@section('title', 'Transfer stock')
@section('heading', 'Warehouse transfer')
@section('subheading', 'Move stock between warehouses (FEFO on the source batch)')

@section('content')
@php $field = 'w-full h-11 px-3.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none text-sm'; $label='block text-sm font-medium text-slate-700 mb-1.5'; @endphp
<form method="POST" action="/inventory/transfer" class="max-w-xl">
    @csrf
    <x-card class="space-y-5">
        <div>
            <label class="{{ $label }}">Product</label>
            <select name="product_id" class="{{ $field }}" required>
                @foreach ($products as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>@endforeach
            </select>
        </div>
        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="{{ $label }}">From warehouse</label>
                <select name="from_warehouse_id" class="{{ $field }}" required>
                    @foreach ($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">To warehouse</label>
                <select name="to_warehouse_id" class="{{ $field }}" required>
                    @foreach ($warehouses as $w)<option value="{{ $w->id }}" @selected($loop->last)>{{ $w->name }}</option>@endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="{{ $label }}">Quantity</label>
            <input name="quantity" type="number" min="1" value="{{ old('quantity') }}" class="{{ $field }}" required>
        </div>
        <div class="flex items-center gap-3 pt-1">
            <button class="h-11 px-6 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold shadow-sm shadow-brand-200">Record transfer</button>
            <a href="/inventory" class="h-11 px-5 grid place-items-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium">Cancel</a>
        </div>
    </x-card>
</form>
@endsection
