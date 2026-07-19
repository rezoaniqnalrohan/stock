@extends('layouts.app')
@section('title', $ingredient->exists ? 'Edit ingredient' : 'Add ingredient')
@section('content')
<div class="max-w-lg rounded-3xl bg-white p-6 shadow-sm">
    <form method="POST" action="{{ $ingredient->exists ? route('ingredients.update', $ingredient) : route('ingredients.store') }}" class="space-y-4">
        @csrf
        @if ($ingredient->exists) @method('PUT') @endif

        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium">Name <span class="text-brand">*</span></label>
            <input id="name" name="name" value="{{ old('name', $ingredient->name) }}" required
                   class="w-full rounded-xl border border-gray-200 bg-canvas px-4 py-2.5 text-sm outline-none transition focus:border-ink focus:bg-white">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="category" class="mb-1.5 block text-sm font-medium">Category <span class="text-brand">*</span></label>
                <input id="category" name="category" value="{{ old('category', $ingredient->category) }}" required list="categories"
                       class="w-full rounded-xl border border-gray-200 bg-canvas px-4 py-2.5 text-sm outline-none transition focus:border-ink focus:bg-white">
                <datalist id="categories">
                    @foreach (['Meat', 'Bakery', 'Dairy', 'Produce', 'Pantry', 'Sauces', 'Beverages', 'Packaging'] as $cat)
                        <option value="{{ $cat }}">
                    @endforeach
                </datalist>
            </div>
            <div>
                <label for="unit" class="mb-1.5 block text-sm font-medium">Unit <span class="text-brand">*</span></label>
                <input id="unit" name="unit" value="{{ old('unit', $ingredient->unit) }}" required list="units" placeholder="kg / L / pcs"
                       class="w-full rounded-xl border border-gray-200 bg-canvas px-4 py-2.5 text-sm outline-none transition focus:border-ink focus:bg-white">
                <datalist id="units"><option value="kg"><option value="L"><option value="pcs"></datalist>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="cost" class="mb-1.5 block text-sm font-medium">Cost per unit (৳) <span class="text-brand">*</span></label>
                <input id="cost" name="cost" type="number" step="0.01" min="0" value="{{ old('cost', $ingredient->cost) }}" required
                       class="num w-full rounded-xl border border-gray-200 bg-canvas px-4 py-2.5 text-sm outline-none transition focus:border-ink focus:bg-white">
                <p class="mt-1 text-xs text-gray-400">Updated automatically on each purchase.</p>
            </div>
            <div>
                <label for="reorder_level" class="mb-1.5 block text-sm font-medium">Reorder level <span class="text-brand">*</span></label>
                <input id="reorder_level" name="reorder_level" type="number" step="0.01" min="0" value="{{ old('reorder_level', $ingredient->reorder_level) }}" required
                       class="num w-full rounded-xl border border-gray-200 bg-canvas px-4 py-2.5 text-sm outline-none transition focus:border-ink focus:bg-white">
                <p class="mt-1 text-xs text-gray-400">Low-stock alert when stock falls to this.</p>
            </div>
        </div>

        @if ($ingredient->exists)
            <p class="rounded-xl bg-canvas px-4 py-3 text-sm text-gray-500">
                Current stock: <strong class="num text-ink">{{ $ingredient->stock + 0 }} {{ $ingredient->unit }}</strong> —
                change it via <a href="{{ route('purchases.index') }}" class="font-medium text-brand hover:underline">purchases</a>,
                <a href="{{ route('waste.index') }}" class="font-medium text-brand hover:underline">waste</a> or an
                <a href="{{ route('movements.index') }}" class="font-medium text-brand hover:underline">adjustment</a>.
            </p>
        @endif

        <div class="flex gap-3 pt-2">
            <button class="cursor-pointer rounded-full bg-ink px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-black">
                {{ $ingredient->exists ? 'Save changes' : 'Add ingredient' }}
            </button>
            <a href="{{ route('ingredients.index') }}" class="rounded-full px-6 py-2.5 text-sm font-semibold text-gray-500 transition hover:bg-canvas">Cancel</a>
        </div>
    </form>
</div>
@endsection
