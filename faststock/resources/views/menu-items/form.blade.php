@extends('layouts.app')
@section('title', $item->exists ? 'Edit menu item' : 'Add menu item')
@section('content')
<div class="max-w-2xl rounded-3xl bg-white p-6 shadow-sm">
    <form method="POST" action="{{ $item->exists ? route('menu-items.update', $item) : route('menu-items.store') }}">
        @csrf
        @if ($item->exists) @method('PUT') @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium">Name <span class="text-brand">*</span></label>
                <input id="name" name="name" value="{{ old('name', $item->name) }}" required
                       class="w-full rounded-xl border border-gray-200 bg-canvas px-4 py-2.5 text-sm outline-none transition focus:border-ink focus:bg-white">
            </div>
            <div>
                <label for="price" class="mb-1.5 block text-sm font-medium">Selling price (৳) <span class="text-brand">*</span></label>
                <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $item->price) }}" required
                       class="num w-full rounded-xl border border-gray-200 bg-canvas px-4 py-2.5 text-sm outline-none transition focus:border-ink focus:bg-white">
            </div>
        </div>

        <fieldset class="mt-6">
            <legend class="mb-1 text-sm font-semibold">Recipe</legend>
            <p class="mb-3 text-xs text-gray-400">Quantities consumed per single item sold. Selling deducts these from ingredient stock.</p>
            <div id="recipe-rows" class="space-y-2">
                @foreach ($item->ingredients as $i => $ingredient)
                    <div class="flex items-center gap-2">
                        <select name="recipe[{{ $i }}][ingredient_id]" aria-label="Ingredient" class="flex-1 rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
                            <option value="">— ingredient —</option>
                            @foreach ($ingredients as $option)
                                <option value="{{ $option->id }}" @selected($option->id === $ingredient->id)>{{ $option->name }} ({{ $option->unit }})</option>
                            @endforeach
                        </select>
                        <input name="recipe[{{ $i }}][qty]" type="number" step="0.001" min="0" value="{{ $ingredient->pivot->qty + 0 }}" aria-label="Quantity" placeholder="qty"
                               class="num w-28 rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
                        <button type="button" onclick="this.parentElement.remove()" aria-label="Remove row"
                                class="grid h-9 w-9 shrink-0 cursor-pointer place-items-center rounded-lg text-gray-400 transition hover:bg-brand/10 hover:text-brand"><x-icon name="x" class="h-4 w-4"/></button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-row"
                    class="mt-3 flex cursor-pointer items-center gap-2 rounded-full border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 transition hover:border-ink hover:text-ink">
                <x-icon name="plus" class="h-4 w-4"/> Add ingredient row
            </button>
        </fieldset>

        <div class="mt-6 flex gap-3">
            <button class="cursor-pointer rounded-full bg-ink px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-black">
                {{ $item->exists ? 'Save changes' : 'Add menu item' }}
            </button>
            <a href="{{ route('menu-items.index') }}" class="rounded-full px-6 py-2.5 text-sm font-semibold text-gray-500 transition hover:bg-canvas">Cancel</a>
        </div>
    </form>
</div>

<template id="row-template">
    <div class="flex items-center gap-2">
        <select aria-label="Ingredient" class="flex-1 rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
            <option value="">— ingredient —</option>
            @foreach ($ingredients as $option)
                <option value="{{ $option->id }}">{{ $option->name }} ({{ $option->unit }})</option>
            @endforeach
        </select>
        <input type="number" step="0.001" min="0" aria-label="Quantity" placeholder="qty"
               class="num w-28 rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
        <button type="button" onclick="this.parentElement.remove()" aria-label="Remove row"
                class="grid h-9 w-9 shrink-0 cursor-pointer place-items-center rounded-lg text-gray-400 transition hover:bg-brand/10 hover:text-brand"><x-icon name="x" class="h-4 w-4"/></button>
    </div>
</template>

@endsection

@push('scripts')
<script>
    let rowIndex = {{ $item->ingredients->count() }};
    document.getElementById('add-row').addEventListener('click', () => {
        const row = document.getElementById('row-template').content.cloneNode(true);
        row.querySelector('select').name = `recipe[${rowIndex}][ingredient_id]`;
        row.querySelector('input').name = `recipe[${rowIndex}][qty]`;
        document.getElementById('recipe-rows').appendChild(row);
        rowIndex++;
    });
    if (rowIndex === 0) document.getElementById('add-row').click();
</script>
@endpush
