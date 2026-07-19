@extends('layouts.app')
@section('title', 'New Purchase Order')

@section('content')
<x-page-header title="New Purchase Order" subtitle="Order stock from a supplier">
    <x-slot:actions>
        <a href="{{ route('purchase-orders.index') }}" class="rounded-xl border border-line text-sm font-medium px-4 py-2.5 hover:bg-white">Back</a>
    </x-slot:actions>
</x-page-header>

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('purchase-orders.store') }}">
        @csrf
        <div class="grid sm:grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">Supplier</label>
                <select name="supplier_id" required class="w-full rounded-xl border border-line px-3 py-2.5 text-sm">
                    @foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Deliver to</label>
                <select name="outlet_id" required class="w-full rounded-xl border border-line px-3 py-2.5 text-sm">
                    @foreach ($outlets as $o)<option value="{{ $o->id }}" @selected($o->is_warehouse)>{{ $o->name }}</option>@endforeach
                </select>
            </div>
        </div>

        <label class="block text-sm font-medium mb-2">Line items</label>
        <div id="lines" class="space-y-2"></div>
        <button type="button" onclick="addLine()" class="mt-3 rounded-lg border border-line text-sm font-medium px-4 py-2 hover:bg-cream">+ Add line</button>

        <div class="flex items-center justify-between mt-6 pt-5 border-t border-line">
            <span class="text-sm text-muted">Estimated total: <span id="poTotal" class="font-display text-lg font-bold text-ink">$0.00</span></span>
            <button class="rounded-xl bg-sidebar text-white text-sm font-semibold px-5 py-2.5 hover:bg-black transition">Create purchase order</button>
        </div>
    </form>
</x-card>

@push('scripts')
<script>
    const products = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name.' — '.$p->variant, 'cost' => (float) $p->cost]));
    let idx = 0;

    function addLine() {
        const i = idx++;
        const opts = products.map(p => `<option value="${p.id}" data-cost="${p.cost}">${p.name}</option>`).join('');
        const row = document.createElement('div');
        row.className = 'flex flex-wrap gap-2 items-center';
        row.innerHTML = `
            <select name="items[${i}][product_id]" onchange="syncCost(this)" required class="flex-1 min-w-[180px] rounded-xl border border-line px-3 py-2 text-sm">${opts}</select>
            <input name="items[${i}][quantity]" type="number" min="1" value="1" oninput="recalc()" class="w-20 rounded-xl border border-line px-3 py-2 text-sm" placeholder="Qty">
            <input name="items[${i}][cost]" type="number" step="0.01" min="0" oninput="recalc()" class="w-28 rounded-xl border border-line px-3 py-2 text-sm" placeholder="Unit cost">
            <button type="button" onclick="this.parentElement.remove(); recalc()" class="text-red-600 px-2 text-lg">×</button>`;
        document.getElementById('lines').appendChild(row);
        syncCost(row.querySelector('select'));
    }
    function syncCost(sel) {
        const cost = sel.selectedOptions[0].dataset.cost;
        sel.parentElement.querySelector('input[type=number][step]').value = cost;
        recalc();
    }
    function recalc() {
        let total = 0;
        document.querySelectorAll('#lines > div').forEach(r => {
            const qty = +r.querySelector('input[type=number]:not([step])').value || 0;
            const cost = +r.querySelector('input[step]').value || 0;
            total += qty * cost;
        });
        document.getElementById('poTotal').textContent = '$' + total.toFixed(2);
    }
    addLine();
</script>
@endpush
@endsection
