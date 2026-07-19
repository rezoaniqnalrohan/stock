@extends('layouts.app')
@section('title', 'Ingredients')
@section('content')
<div class="rounded-3xl bg-white p-6 shadow-sm">
    <div class="mb-5 flex flex-wrap items-center gap-3">
        <form method="GET" class="flex-1 min-w-48">
            <label for="q" class="sr-only">Search ingredients</label>
            <input id="q" type="search" name="q" value="{{ $q }}" placeholder="Search ingredients…"
                   class="w-full max-w-xs rounded-xl border border-gray-200 bg-canvas px-4 py-2.5 text-sm outline-none transition focus:border-ink focus:bg-white">
        </form>
        <a href="{{ route('ingredients.create') }}"
           class="flex items-center gap-2 rounded-full bg-ink px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-black">
            <x-icon name="plus" class="h-4 w-4"/> Add ingredient
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-medium uppercase tracking-wide text-gray-400">
                    <th class="pb-3">Name</th><th class="pb-3">Category</th><th class="pb-3 text-right">Stock</th>
                    <th class="pb-3 text-right">Reorder at</th><th class="pb-3 text-right">Unit cost</th>
                    <th class="pb-3 text-right">Value</th><th class="pb-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ingredients as $ingredient)
                    <tr class="border-t border-gray-100">
                        <td class="py-3 font-medium">{{ $ingredient->name }}
                            @if ($ingredient->isLow())
                                <span class="ms-1 inline-flex items-center gap-1 rounded-full bg-brand/10 px-2 py-0.5 text-[11px] font-semibold text-brand"><x-icon name="alert" class="h-3 w-3"/>Low</span>
                            @endif
                        </td>
                        <td class="py-3 text-gray-500">{{ $ingredient->category }}</td>
                        <td class="num py-3 text-right font-semibold {{ $ingredient->isLow() ? 'text-brand' : '' }}">{{ $ingredient->stock + 0 }} {{ $ingredient->unit }}</td>
                        <td class="num py-3 text-right text-gray-500">{{ $ingredient->reorder_level + 0 }} {{ $ingredient->unit }}</td>
                        <td class="num py-3 text-right">৳{{ number_format($ingredient->cost, 2) }}</td>
                        <td class="num py-3 text-right">৳{{ number_format($ingredient->stock * $ingredient->cost) }}</td>
                        <td class="py-3">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('ingredients.edit', $ingredient) }}" aria-label="Edit {{ $ingredient->name }}"
                                   class="grid h-8 w-8 place-items-center rounded-lg text-gray-400 transition hover:bg-canvas hover:text-ink"><x-icon name="pencil" class="h-4 w-4"/></a>
                                @can('admin')
                                    <form method="POST" action="{{ route('ingredients.destroy', $ingredient) }}"
                                          onsubmit="return confirm('Delete {{ $ingredient->name }}? Its movement history goes with it.')">
                                        @csrf @method('DELETE')
                                        <button aria-label="Delete {{ $ingredient->name }}"
                                                class="grid h-8 w-8 cursor-pointer place-items-center rounded-lg text-gray-400 transition hover:bg-brand/10 hover:text-brand"><x-icon name="trash" class="h-4 w-4"/></button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-10 text-center text-gray-400">No ingredients{{ $q ? " matching \"$q\"" : '' }}. Add your first one above.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $ingredients->links() }}</div>
</div>
@endsection
