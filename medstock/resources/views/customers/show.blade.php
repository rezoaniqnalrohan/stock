<x-layouts.app title="{{ $customer->name }}" subtitle="{{ ucfirst($customer->type) }}">
    <x-slot:action>
        <a href="{{ route('customers.edit', $customer) }}" class="rounded-full bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Edit</a>
    </x-slot:action>
    <div class="grid lg:grid-cols-3 gap-6">
        <x-card title="Contact">
            <dl class="space-y-3 text-sm">
                <div><dt class="text-slate-400">Type</dt><dd class="font-medium text-slate-800">{{ ucfirst($customer->type) }}</dd></div>
                <div><dt class="text-slate-400">Email</dt><dd class="font-medium text-slate-800">{{ $customer->email ?? '—' }}</dd></div>
                <div><dt class="text-slate-400">Phone</dt><dd class="font-medium text-slate-800">{{ $customer->phone ?? '—' }}</dd></div>
                <div><dt class="text-slate-400">Address</dt><dd class="font-medium text-slate-800">{{ $customer->address ?? '—' }}</dd></div>
            </dl>
        </x-card>
        <div class="lg:col-span-2">
            <x-card title="Recent Orders" padding="p-0">
                <div class="divide-y divide-slate-50">
                    @forelse ($customer->salesOrders()->latest('order_date')->get() as $so)
                        <a href="{{ route('sales-orders.show', $so) }}" class="flex items-center justify-between px-5 py-3 hover:bg-slate-50/60">
                            <div><p class="font-medium text-slate-800">Order #{{ str_pad($so->id,5,'0',STR_PAD_LEFT) }}</p><p class="text-xs text-slate-400">{{ $so->order_date->format('M j, Y') }}</p></div>
                            <div class="flex items-center gap-3"><span class="font-semibold tabular-nums">${{ number_format($so->total,0) }}</span><x-pill :status="$so->status" /></div>
                        </a>
                    @empty
                        <p class="px-5 py-8 text-center text-slate-400">No orders yet.</p>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.app>
