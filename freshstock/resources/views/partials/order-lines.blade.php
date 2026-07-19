{{-- Reusable line-item repeater. Params: $products, $priceName, $priceLabel --}}
<div>
    <div class="flex items-center justify-between mb-2">
        <label class="text-sm font-medium text-slate-700">Line items</label>
        <button type="button" onclick="addLine()" class="text-sm text-brand-600 font-medium hover:underline">+ Add item</button>
    </div>
    <div id="lines" class="space-y-2"></div>
</div>

<template id="lineTpl">
    <div class="line grid grid-cols-12 gap-2 items-center">
        <select name="items[__i__][product_id]" class="col-span-6 h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none" required>
            @foreach ($products as $p)<option value="{{ $p->id }}" data-price="{{ $priceName === 'unit_cost' ? $p->cost : $p->price }}">{{ $p->name }}</option>@endforeach
        </select>
        <input name="items[__i__][quantity]" type="number" min="1" value="1" class="col-span-2 h-10 px-2 rounded-lg border border-slate-200 text-sm text-right focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none" required>
        <input name="items[__i__][{{ $priceName }}]" type="number" step="0.01" class="col-span-3 h-10 px-2 rounded-lg border border-slate-200 text-sm text-right focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none" placeholder="{{ $priceLabel }}" required>
        <button type="button" onclick="this.closest('.line').remove()" class="col-span-1 h-10 grid place-items-center text-slate-400 hover:text-rose-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</template>

<script>
    let lineIndex = 0;
    function addLine() {
        const tpl = document.getElementById('lineTpl').innerHTML.replaceAll('__i__', lineIndex++);
        const div = document.createElement('div');
        div.innerHTML = tpl;
        const row = div.firstElementChild;
        // prefill price from selected product
        const sel = row.querySelector('select'), price = row.querySelector('input[type=number][step]');
        const sync = () => price.value = sel.selectedOptions[0].dataset.price;
        sel.addEventListener('change', sync); sync();
        document.getElementById('lines').appendChild(row);
    }
    addLine(); // start with one row
</script>
