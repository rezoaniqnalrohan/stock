@extends('layouts.app')
@section('title', 'New sales order')
@section('heading', 'New sales order')
@section('subheading', 'Create an order for a customer')

@section('content')
@php $field = 'w-full h-11 px-3.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none text-sm'; $label='block text-sm font-medium text-slate-700 mb-1.5'; @endphp
<form method="POST" action="/orders" class="max-w-3xl">
    @csrf
    <x-card class="space-y-5">
        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="{{ $label }}">Customer</label>
                <select name="customer_id" class="{{ $field }}" required>
                    @foreach ($customers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Ship from</label>
                <select name="warehouse_id" class="{{ $field }}" required>
                    @foreach ($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
                </select>
            </div>
        </div>

        @include('partials.order-lines', ['products' => $products, 'priceName' => 'unit_price', 'priceLabel' => 'Unit price'])

        <div class="flex items-center gap-3 pt-1">
            <button class="h-11 px-6 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold shadow-sm shadow-brand-200">Create order</button>
            <a href="/orders" class="h-11 px-5 grid place-items-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium">Cancel</a>
        </div>
    </x-card>
</form>
@endsection
