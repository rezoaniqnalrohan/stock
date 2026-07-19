@extends('layouts.app')
@section('title', $customer->exists ? 'Edit customer' : 'New customer')
@section('heading', $customer->exists ? 'Edit customer' : 'New customer')

@section('content')
@php $field = 'w-full h-11 px-3.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none text-sm'; $label='block text-sm font-medium text-slate-700 mb-1.5'; @endphp
<form method="POST" action="{{ $customer->exists ? '/customers/'.$customer->id : '/customers' }}" class="max-w-2xl">
    @csrf
    @if ($customer->exists) @method('PUT') @endif
    <x-card class="space-y-5">
        <div>
            <label class="{{ $label }}">Company name</label>
            <input name="name" value="{{ old('name', $customer->name) }}" class="{{ $field }}" required>
        </div>
        <div class="grid sm:grid-cols-2 gap-5">
            <div><label class="{{ $label }}">Contact person</label><input name="contact_name" value="{{ old('contact_name', $customer->contact_name) }}" class="{{ $field }}"></div>
            <div><label class="{{ $label }}">Email</label><input name="email" type="email" value="{{ old('email', $customer->email) }}" class="{{ $field }}"></div>
            <div><label class="{{ $label }}">Phone</label><input name="phone" value="{{ old('phone', $customer->phone) }}" class="{{ $field }}"></div>
            <div><label class="{{ $label }}">Location</label><input name="address" value="{{ old('address', $customer->address) }}" class="{{ $field }}"></div>
        </div>
        <div class="flex items-center gap-3 pt-1">
            <button class="h-11 px-6 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold shadow-sm shadow-brand-200">{{ $customer->exists ? 'Save changes' : 'Create customer' }}</button>
            <a href="/customers" class="h-11 px-5 grid place-items-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium">Cancel</a>
        </div>
    </x-card>
</form>
@endsection
