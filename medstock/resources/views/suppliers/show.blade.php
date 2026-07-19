<x-layouts.app title="{{ $supplier->name }}" subtitle="Supplier">
    <x-slot:action>
        <a href="{{ route('suppliers.edit', $supplier) }}" class="rounded-full bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Edit</a>
    </x-slot:action>
    <div class="grid lg:grid-cols-3 gap-6">
        <x-card title="Contact">
            <dl class="space-y-3 text-sm">
                <div><dt class="text-slate-400">Contact Person</dt><dd class="font-medium text-slate-800">{{ $supplier->contact_name ?? '—' }}</dd></div>
                <div><dt class="text-slate-400">Email</dt><dd class="font-medium text-slate-800">{{ $supplier->email ?? '—' }}</dd></div>
                <div><dt class="text-slate-400">Phone</dt><dd class="font-medium text-slate-800">{{ $supplier->phone ?? '—' }}</dd></div>
                <div><dt class="text-slate-400">Address</dt><dd class="font-medium text-slate-800">{{ $supplier->address ?? '—' }}</dd></div>
            </dl>
        </x-card>
        <div class="lg:col-span-2">
            <x-card title="Products Supplied" subtitle="{{ $supplier->products->count() }} items" padding="p-0">
                <div class="divide-y divide-slate-50">
                    @forelse ($supplier->products as $p)
                        <a href="{{ route('products.show', $p) }}" class="flex items-center justify-between px-5 py-3 hover:bg-slate-50/60">
                            <div><p class="font-medium text-slate-800">{{ $p->name }}</p><p class="text-xs text-slate-400 font-mono">{{ $p->sku }}</p></div>
                            <span class="text-sm text-slate-500">{{ $p->category->name }}</span>
                        </a>
                    @empty
                        <p class="px-5 py-8 text-center text-slate-400">No products linked.</p>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.app>
