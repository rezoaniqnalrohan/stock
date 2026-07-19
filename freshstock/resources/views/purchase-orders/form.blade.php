@extends('layouts.app')
@section('title', 'New purchase order')
@section('heading', 'New purchase order')
@section('subheading', 'Raise an order to a supplier')

@section('content')
@php $field = 'w-full h-11 px-3.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none text-sm'; $label='block text-sm font-medium text-slate-700 mb-1.5'; @endphp
<form method="POST" action="/purchase-orders" class="max-w-3xl">
    @csrf
    <x-card class="space-y-5">
        <div class="grid sm:grid-cols-3 gap-5">
            <div>
                <label class="{{ $label }}">Supplier</label>
                <select name="supplier_id" class="{{ $field }}" required>
                    @foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Deliver to</label>
                <select name="warehouse_id" class="{{ $field }}" required>
                    @foreach ($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Expected date</label>
                <input name="expected_date" type="date" value="{{ now()->addDays(5)->format('Y-m-d') }}" class="{{ $field }}">
            </div>
        </div>

        @include('partials.order-lines', ['products' => $products, 'priceName' => 'unit_cost', 'priceLabel' => 'Unit cost'])

        <div class="flex items-center gap-3 pt-1">
            <button class="h-11 px-6 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold shadow-sm shadow-brand-200">Create purchase order</button>
            <a href="/purchase-orders" class="h-11 px-5 grid place-items-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium">Cancel</a>
        </div>
    </x-card>
</form>
@endsection
