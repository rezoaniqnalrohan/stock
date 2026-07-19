@extends('layouts.app')
@section('title', 'Waste & spoilage')
@section('content')
<div class="grid gap-5 lg:grid-cols-[320px_1fr]">
    <div class="h-fit rounded-3xl bg-white p-6 shadow-sm lg:sticky lg:top-6">
        <h2 class="mb-4 font-semibold">Log waste</h2>
        <form method="POST" action="{{ route('waste.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="ingredient_id" class="mb-1.5 block text-sm font-medium">Ingredient <span class="text-brand">*</span></label>
                <select id="ingredient_id" name="ingredient_id" required class="w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
                    <option value="">— select —</option>
                    @foreach ($ingredients as $ingredient)
                        <option value="{{ $ingredient->id }}" @selected(old('ingredient_id') == $ingredient->id)>{{ $ingredient->name }} — {{ $ingredient->stock + 0 }} {{ $ingredient->unit }} in stock</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="qty" class="mb-1.5 block text-sm font-medium">Quantity wasted <span class="text-brand">*</span></label>
                <input id="qty" name="qty" type="number" step="0.01" min="0.01" value="{{ old('qty') }}" required
                       class="num w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
            </div>
            <div>
                <label for="note" class="mb-1.5 block text-sm font-medium">Reason <span class="text-brand">*</span></label>
                <input id="note" name="note" value="{{ old('note') }}" required placeholder="expired / burnt / dropped…"
                       class="w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
            </div>
            <button class="w-full cursor-pointer rounded-full bg-ink py-3 text-sm font-semibold text-white transition hover:bg-black">Record waste</button>
        </form>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm">
        <h2 class="mb-4 font-semibold">Waste log</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-medium uppercase tracking-wide text-gray-400">
                        <th class="pb-3">Date</th><th class="pb-3">Ingredient</th><th class="pb-3 text-right">Qty</th>
                        <th class="pb-3 text-right">Cost lost</th><th class="pb-3">Reason</th><th class="pb-3">By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($waste as $entry)
                        <tr class="border-t border-gray-100">
                            <td class="num py-3 text-gray-500">{{ $entry->created_at->format('d M, H:i') }}</td>
                            <td class="py-3 font-medium">{{ $entry->ingredient->name }}</td>
                            <td class="num py-3 text-right font-semibold text-brand">{{ abs($entry->qty) + 0 }} {{ $entry->ingredient->unit }}</td>
                            <td class="num py-3 text-right text-gray-500">৳{{ number_format(abs($entry->qty) * $entry->ingredient->cost) }}</td>
                            <td class="py-3 text-gray-500">{{ $entry->note }}</td>
                            <td class="py-3 text-gray-500">{{ $entry->user?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-gray-400">No waste recorded — great kitchen discipline.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $waste->links() }}</div>
    </div>
</div>
@endsection
