@php $p = $product ?? null; @endphp
<x-card>
    <div class="grid sm:grid-cols-2 gap-5">
        <x-field label="Product Name" name="name" :value="$p?->name" required />
        <x-field label="SKU" name="sku" :value="$p?->sku" required placeholder="MSK-N95-001" />
        <x-field label="Category" name="category_id" type="select" required
                 :options="$categories->pluck('name','id')" :selected="$p?->category_id" placeholder="Select category" />
        <x-field label="Unit of Measure" name="unit_id" type="select" required
                 :options="$units->mapWithKeys(fn($u)=>[$u->id=>$u->name.' ('.$u->abbreviation.')'])" :selected="$p?->unit_id" placeholder="Select unit" />
        <x-field label="Supplier" name="supplier_id" type="select"
                 :options="$suppliers->pluck('name','id')" :selected="$p?->supplier_id" placeholder="No supplier" />
        <x-field label="Barcode / Lot" name="barcode" :value="$p?->barcode" placeholder="Scan or enter code" />
        <x-field label="Cost per Unit" name="cost" type="number" step="0.01" :value="$p?->cost ?? '0.00'" required />
        <x-field label="Price per Unit" name="price" type="number" step="0.01" :value="$p?->price ?? '0.00'" required />
        <x-field label="Reorder Point" name="reorder_point" type="number" :value="$p?->reorder_point ?? 0" required />
        <div class="sm:col-span-2">
            <x-field label="Description" name="description" type="textarea" :value="$p?->description" />
        </div>
    </div>
    <div class="mt-6 flex items-center gap-3">
        <button class="rounded-lg bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-teal-700">{{ $submit }}</button>
        <a href="{{ route('products.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</a>
    </div>
</x-card>
