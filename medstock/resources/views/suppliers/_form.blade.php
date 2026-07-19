@php $s = $supplier ?? null; @endphp
<x-card>
    <div class="grid sm:grid-cols-2 gap-5">
        <x-field label="Supplier Name" name="name" :value="$s?->name" required />
        <x-field label="Contact Person" name="contact_name" :value="$s?->contact_name" />
        <x-field label="Email" name="email" type="email" :value="$s?->email" />
        <x-field label="Phone" name="phone" :value="$s?->phone" />
        <div class="sm:col-span-2"><x-field label="Address" name="address" :value="$s?->address" /></div>
    </div>
    <div class="mt-6 flex gap-3">
        <button class="rounded-lg bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-teal-700">{{ $submit }}</button>
        <a href="{{ route('suppliers.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</a>
    </div>
</x-card>
