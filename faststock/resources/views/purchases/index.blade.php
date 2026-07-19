@extends('layouts.app')
@section('title', 'Purchases')
@section('content')
<div class="grid gap-5 lg:grid-cols-[320px_1fr]">
    <div class="h-fit rounded-3xl bg-white p-6 shadow-sm lg:sticky lg:top-6">
        <h2 class="mb-4 font-semibold">Record purchase</h2>
        <form method="POST" action="{{ route('purchases.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="ingredient_id" class="mb-1.5 block text-sm font-medium">Ingredient <span class="text-brand">*</span></label>
                <select id="ingredient_id" name="ingredient_id" required class="w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
                    <option value="">— select —</option>
                    @foreach ($ingredients as $ingredient)
                        <option value="{{ $ingredient->id }}" @selected(old('ingredient_id') == $ingredient->id)>{{ $ingredient->name }} ({{ $ingredient->unit }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="supplier_id" class="mb-1.5 block text-sm font-medium">Supplier</label>
                <select id="supplier_id" name="supplier_id" class="w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
                    <option value="">— none —</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="qty" class="mb-1.5 block text-sm font-medium">Quantity <span class="text-brand">*</span></label>
                    <input id="qty" name="qty" type="number" step="0.01" min="0.01" value="{{ old('qty') }}" required
                           class="num w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
                </div>
                <div>
                    <label for="unit_cost" class="mb-1.5 block text-sm font-medium">Unit cost (৳) <span class="text-brand">*</span></label>
                    <input id="unit_cost" name="unit_cost" type="number" step="0.01" min="0" value="{{ old('unit_cost') }}" required
                           class="num w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
                </div>
            </div>
            <div>
                <label for="expiry_date" class="mb-1.5 block text-sm font-medium">Expiry date</label>
                <input id="expiry_date" name="expiry_date" type="date" value="{{ old('expiry_date') }}" min="{{ now()->addDay()->toDateString() }}"
                       class="w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
                <p class="mt-1 text-xs text-gray-400">For perishables — feeds the expiry warnings.</p>
            </div>
            <button class="w-full cursor-pointer rounded-full bg-ink py-3 text-sm font-semibold text-white transition hover:bg-black">Add stock</button>
        </form>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm">
        <h2 class="mb-4 font-semibold">Recent purchases</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-medium uppercase tracking-wide text-gray-400">
                        <th class="pb-3">Date</th><th class="pb-3">Ingredient</th><th class="pb-3 text-right">Qty</th>
                        <th class="pb-3 text-right">Unit cost</th><th class="pb-3 text-right">Total</th><th class="pb-3">Supplier</th><th class="pb-3">Expiry</th><th class="pb-3">By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                        <tr class="border-t border-gray-100">
                            <td class="num py-3 text-gray-500">{{ $purchase->created_at->format('d M, H:i') }}</td>
                            <td class="py-3 font-medium">{{ $purchase->ingredient->name }}</td>
                            <td class="num py-3 text-right">{{ $purchase->qty + 0 }} {{ $purchase->ingredient->unit }}</td>
                            <td class="num py-3 text-right">৳{{ number_format($purchase->unit_cost, 2) }}</td>
                            <td class="num py-3 text-right font-semibold">৳{{ number_format($purchase->qty * $purchase->unit_cost) }}</td>
                            <td class="py-3 text-gray-500">{{ $purchase->supplier?->name ?? '—' }}</td>
                            <td class="num py-3 {{ $purchase->expiry_date?->isPast() ? 'font-semibold text-brand' : 'text-gray-500' }}">{{ $purchase->expiry_date?->format('d M Y') ?? '—' }}</td>
                            <td class="py-3 text-gray-500">{{ $purchase->user?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-10 text-center text-gray-400">No purchases yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $purchases->links() }}</div>
    </div>
</div>
@endsection
