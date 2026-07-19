@extends('layouts.app')
@section('title', 'Menu items')
@section('content')
<div class="rounded-3xl bg-white p-6 shadow-sm">
    <div class="mb-5 flex items-center justify-between gap-3">
        <p class="text-sm text-gray-500">Each item's recipe drives automatic ingredient deduction on every sale.</p>
        <a href="{{ route('menu-items.create') }}"
           class="flex shrink-0 items-center gap-2 rounded-full bg-ink px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-black">
            <x-icon name="plus" class="h-4 w-4"/> Add menu item
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-medium uppercase tracking-wide text-gray-400">
                    <th class="pb-3">Item</th><th class="pb-3 text-right">Price</th><th class="pb-3 text-right">Ingredient cost</th>
                    <th class="pb-3 text-right">Margin</th><th class="pb-3 text-right">Can make</th><th class="pb-3">Recipe</th><th class="pb-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    @php
                        $cost = $item->ingredients->sum(fn ($i) => $i->pivot->qty * $i->cost);
                        $available = $item->ingredients->isEmpty() ? null : $item->available();
                    @endphp
                    <tr class="border-t border-gray-100">
                        <td class="py-3 font-medium">{{ $item->name }}</td>
                        <td class="num py-3 text-right">৳{{ number_format($item->price) }}</td>
                        <td class="num py-3 text-right text-gray-500">৳{{ number_format($cost, 2) }}</td>
                        <td class="num py-3 text-right font-semibold {{ $item->price - $cost >= 0 ? 'text-green-600' : 'text-brand' }}">৳{{ number_format($item->price - $cost) }}</td>
                        <td class="num py-3 text-right">
                            @if (is_null($available))
                                <span class="text-gray-400">no recipe</span>
                            @else
                                <span class="{{ $available < 10 ? 'font-semibold text-brand' : '' }}">{{ $available }}</span>
                            @endif
                        </td>
                        <td class="max-w-72 truncate py-3 text-gray-500">{{ $item->ingredients->pluck('name')->join(', ') ?: '—' }}</td>
                        <td class="py-3">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('menu-items.edit', $item) }}" aria-label="Edit {{ $item->name }}"
                                   class="grid h-8 w-8 place-items-center rounded-lg text-gray-400 transition hover:bg-canvas hover:text-ink"><x-icon name="pencil" class="h-4 w-4"/></a>
                                @can('admin')
                                    <form method="POST" action="{{ route('menu-items.destroy', $item) }}"
                                          onsubmit="return confirm('Delete {{ $item->name }}?')">
                                        @csrf @method('DELETE')
                                        <button aria-label="Delete {{ $item->name }}"
                                                class="grid h-8 w-8 cursor-pointer place-items-center rounded-lg text-gray-400 transition hover:bg-brand/10 hover:text-brand"><x-icon name="trash" class="h-4 w-4"/></button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-10 text-center text-gray-400">No menu items yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
