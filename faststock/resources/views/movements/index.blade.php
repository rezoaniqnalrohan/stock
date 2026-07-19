@extends('layouts.app')
@section('title', 'Stock movements')
@section('content')
<div class="space-y-5">
    <div class="grid gap-5 lg:grid-cols-[1fr_320px]">
        {{-- Filters --}}
        <form method="GET" class="flex flex-wrap items-end gap-3 rounded-3xl bg-white p-6 shadow-sm">
            <div>
                <label for="f-ingredient" class="mb-1.5 block text-xs font-medium text-gray-500">Ingredient</label>
                <select id="f-ingredient" name="ingredient_id" class="rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
                    <option value="">All</option>
                    @foreach ($ingredients as $ingredient)
                        <option value="{{ $ingredient->id }}" @selected(($filters['ingredient_id'] ?? '') == $ingredient->id)>{{ $ingredient->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="f-type" class="mb-1.5 block text-xs font-medium text-gray-500">Type</label>
                <select id="f-type" name="type" class="rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
                    <option value="">All</option>
                    @foreach (['purchase', 'sale', 'waste', 'adjustment'] as $type)
                        <option value="{{ $type }}" @selected(($filters['type'] ?? '') === $type)>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="f-from" class="mb-1.5 block text-xs font-medium text-gray-500">From</label>
                <input id="f-from" type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
            </div>
            <div>
                <label for="f-to" class="mb-1.5 block text-xs font-medium text-gray-500">To</label>
                <input id="f-to" type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
            </div>
            <button class="cursor-pointer rounded-full bg-ink px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-black">Filter</button>
            <a href="{{ route('reports.export', ['type' => 'movements'] + array_filter($filters)) }}"
               class="flex items-center gap-2 rounded-full border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-600 transition hover:border-ink hover:text-ink">
                <x-icon name="download" class="h-4 w-4"/> CSV
            </a>
        </form>

        {{-- Adjustment --}}
        <form method="POST" action="{{ route('adjustments.store') }}" class="rounded-3xl bg-white p-6 shadow-sm">
            @csrf
            <h2 class="mb-3 flex items-center gap-2 font-semibold"><x-icon name="sliders" class="h-4 w-4 text-gray-400"/>Stock adjustment</h2>
            <div class="space-y-3">
                <label for="a-ingredient" class="sr-only">Ingredient</label>
                <select id="a-ingredient" name="ingredient_id" required class="w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
                    <option value="">— ingredient —</option>
                    @foreach ($ingredients as $ingredient)
                        <option value="{{ $ingredient->id }}">{{ $ingredient->name }} — system says {{ $ingredient->stock + 0 }} {{ $ingredient->unit }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <label for="a-counted" class="sr-only">Counted stock</label>
                    <input id="a-counted" name="counted" type="number" step="0.01" min="0" required placeholder="Physical count"
                           class="num w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
                    <label for="a-note" class="sr-only">Note</label>
                    <input id="a-note" name="note" placeholder="Note (optional)"
                           class="w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
                </div>
                <button class="w-full cursor-pointer rounded-full bg-ink py-2.5 text-sm font-semibold text-white transition hover:bg-black">Adjust to count</button>
            </div>
        </form>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-medium uppercase tracking-wide text-gray-400">
                        <th class="pb-3">Date</th><th class="pb-3">Ingredient</th><th class="pb-3">Type</th>
                        <th class="pb-3 text-right">Qty</th><th class="pb-3">Note</th><th class="pb-3">Supplier</th><th class="pb-3">By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $movement)
                        <tr class="border-t border-gray-100">
                            <td class="num py-3 text-gray-500">{{ $movement->created_at->format('d M, H:i') }}</td>
                            <td class="py-3 font-medium">{{ $movement->ingredient->name }}</td>
                            <td class="py-3">
                                @php $chip = ['purchase' => 'bg-green-100 text-green-700', 'sale' => 'bg-gray-100 text-gray-600', 'waste' => 'bg-brand/10 text-brand', 'adjustment' => 'bg-amber-100 text-amber-700'][$movement->type]; @endphp
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase {{ $chip }}">{{ $movement->type }}</span>
                            </td>
                            <td class="num py-3 text-right font-semibold {{ $movement->qty >= 0 ? 'text-green-600' : 'text-ink' }}">
                                {{ $movement->qty > 0 ? '+' : '' }}{{ $movement->qty + 0 }} {{ $movement->ingredient->unit }}
                            </td>
                            <td class="max-w-56 truncate py-3 text-gray-500">{{ $movement->note ?? '—' }}</td>
                            <td class="py-3 text-gray-500">{{ $movement->supplier?->name ?? '—' }}</td>
                            <td class="py-3 text-gray-500">{{ $movement->user?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-gray-400">No movements match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $movements->links() }}</div>
    </div>
</div>
@endsection
