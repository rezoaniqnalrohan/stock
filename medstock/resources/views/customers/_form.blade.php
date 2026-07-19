@php $c = $customer ?? null; @endphp
<x-card>
    <div class="grid sm:grid-cols-2 gap-5">
        <x-field label="Customer Name" name="name" :value="$c?->name" required />
        <x-field label="Type" name="type" type="select" required
                 :options="['clinic'=>'Clinic','pharmacy'=>'Pharmacy','hospital'=>'Hospital']" :selected="$c?->type ?? 'clinic'" />
        <x-field label="Email" name="email" type="email" :value="$c?->email" />
        <x-field label="Phone" name="phone" :value="$c?->phone" />
        <div class="sm:col-span-2"><x-field label="Address" name="address" :value="$c?->address" /></div>
    </div>
    <div class="mt-6 flex gap-3">
        <button class="rounded-lg bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-teal-700">{{ $submit }}</button>
        <a href="{{ route('customers.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</a>
    </div>
</x-card>
