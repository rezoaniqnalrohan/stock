@extends('layouts.app')
@section('title', 'New shipment')
@section('heading', 'New shipment')
@section('subheading', 'Log an inbound, outbound or transfer movement')

@section('content')
@php $field = 'w-full h-11 px-3.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none text-sm'; $label='block text-sm font-medium text-slate-700 mb-1.5'; @endphp
<form method="POST" action="/shipments" class="max-w-2xl">
    @csrf
    <x-card class="space-y-5">
        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="{{ $label }}">Type</label>
                <select name="type" class="{{ $field }}" required>
                    @foreach (['inbound' => 'Inbound', 'outbound' => 'Outbound', 'transfer' => 'Transfer'] as $v => $t)
                        <option value="{{ $v }}">{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Status</label>
                <select name="status" class="{{ $field }}" required>
                    @foreach (['pending' => 'Pending', 'in_transit' => 'In transit', 'delivered' => 'Delivered'] as $v => $t)
                        <option value="{{ $v }}">{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="{{ $label }}">Origin</label><input name="origin" value="{{ old('origin') }}" class="{{ $field }}"></div>
            <div><label class="{{ $label }}">Destination</label><input name="destination" value="{{ old('destination') }}" class="{{ $field }}"></div>
            <div><label class="{{ $label }}">Ship date</label><input name="ship_date" type="date" value="{{ now()->format('Y-m-d') }}" class="{{ $field }}"></div>
        </div>
        <div class="flex items-center gap-3 pt-1">
            <button class="h-11 px-6 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold shadow-sm shadow-brand-200">Create shipment</button>
            <a href="/shipments" class="h-11 px-5 grid place-items-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium">Cancel</a>
        </div>
    </x-card>
</form>
@endsection
