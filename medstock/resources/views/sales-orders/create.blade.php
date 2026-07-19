<x-layouts.app title="New Sales Order" subtitle="Create an order for a customer">
    <form method="POST" action="{{ route('sales-orders.store') }}" class="max-w-4xl space-y-6">@csrf
        <x-card>
            <div class="grid sm:grid-cols-3 gap-5">
                <x-field label="Customer" name="customer_id" type="select" required :options="$customers->pluck('name','id')" placeholder="Select customer" />
                <x-field label="Ship From" name="warehouse_id" type="select" required :options="$warehouses->pluck('name','id')" placeholder="Warehouse" />
                <x-field label="Order Date" name="order_date" type="date" :value="now()->toDateString()" required />
            </div>
        </x-card>

        <x-card title="Line Items" padding="p-0">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs uppercase tracking-wider text-slate-400 border-b border-slate-100">
                    <th class="px-5 py-3 font-medium">Product</th><th class="px-5 py-3 font-medium w-28">Quantity</th>
                    <th class="px-5 py-3 font-medium w-32">Unit Price</th><th class="px-5 py-3 w-10"></th>
                </tr></thead>
                <tbody id="rows"></tbody>
            </table>
            <div class="px-5 py-4 border-t border-slate-100">
                <button type="button" onclick="addRow()" class="inline-flex items-center gap-1.5 rounded-lg border border-teal-200 bg-teal-50 px-3 py-1.5 text-sm font-medium text-teal-700 hover:bg-teal-100">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg> Add item
                </button>
            </div>
        </x-card>

        <div class="flex gap-3">
            <button class="rounded-lg bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-teal-700">Create Sales Order</button>
            <a href="{{ route('sales-orders.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</a>
        </div>
    </form>

    @push('scripts')
    <script>
        const products = @json($products);
        let i = 0;
        function addRow() {
            const opts = products.map(p => `<option value="${p.id}" data-price="${p.price}">${p.name} (${p.stock} on hand)</option>`).join('');
            const tr = document.createElement('tr');
            tr.className = 'border-b border-slate-50';
            tr.innerHTML = `
                <td class="px-5 py-2"><select name="items[${i}][product_id]" required onchange="this.closest('tr').querySelector('.price').value = this.selectedOptions[0].dataset.price" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white"><option value="">Select product</option>${opts}</select></td>
                <td class="px-5 py-2"><input name="items[${i}][quantity]" type="number" min="1" value="1" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm tabular-nums"></td>
                <td class="px-5 py-2"><input name="items[${i}][price]" type="number" step="0.01" min="0" value="0.00" required class="price w-full rounded-lg border border-slate-300 px-3 py-2 text-sm tabular-nums"></td>
                <td class="px-5 py-2"><button type="button" onclick="this.closest('tr').remove()" class="text-slate-400 hover:text-rose-500"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg></button></td>`;
            document.getElementById('rows').appendChild(tr);
            i++;
        }
        addRow();
    </script>
    @endpush
</x-layouts.app>
