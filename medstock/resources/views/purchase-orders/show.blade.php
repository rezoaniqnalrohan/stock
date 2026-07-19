<x-layouts.app title="PO-{{ str_pad($order->id,4,'0',STR_PAD_LEFT) }}" subtitle="{{ $order->supplier->name }}">
    <x-slot:action>
        @if ($order->status !== 'received')
            <form method="POST" action="{{ route('purchase-orders.receive', $order) }}" onsubmit="return confirm('Receive all items into inventory?')">@csrf
                <button class="inline-flex items-center gap-2 rounded-full bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 shadow-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Receive Stock
                </button>
            </form>
        @endif
    </x-slot:action>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <x-card title="Items" padding="p-0">
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-xs uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <th class="px-5 py-3 font-medium">Product</th><th class="px-5 py-3 font-medium">Qty</th>
                        <th class="px-5 py-3 font-medium">Unit Cost</th><th class="px-5 py-3 font-medium text-right">Subtotal</th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="px-5 py-3"><p class="font-medium text-slate-800">{{ $item->product->name }}</p><p class="text-xs text-slate-400 font-mono">{{ $item->product->sku }}</p></td>
                                <td class="px-5 py-3 tabular-nums">{{ $item->quantity }}</td>
                                <td class="px-5 py-3 tabular-nums">${{ number_format($item->cost,2) }}</td>
                                <td class="px-5 py-3 tabular-nums font-medium text-right">${{ number_format($item->quantity * $item->cost,2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot><tr class="border-t border-slate-100"><td colspan="3" class="px-5 py-3 text-right font-medium text-slate-500">Total</td><td class="px-5 py-3 text-right font-bold text-slate-900 tabular-nums">${{ number_format($order->total,2) }}</td></tr></tfoot>
                </table>
            </x-card>
        </div>
        <div class="space-y-6">
            <x-card title="Summary">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-400">Status</dt><dd><x-pill :status="$order->status" /></dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">Supplier</dt><dd class="font-medium text-slate-800">{{ $order->supplier->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">Warehouse</dt><dd class="font-medium text-slate-800">{{ $order->warehouse->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">Order Date</dt><dd class="font-medium text-slate-800">{{ $order->order_date->format('M j, Y') }}</dd></div>
                </dl>
                @if ($order->status === 'received')
                    <p class="mt-4 rounded-lg bg-emerald-50 px-3 py-2 text-xs text-emerald-700">Stock received into {{ $order->warehouse->name }}.</p>
                @endif
            </x-card>
            <form method="POST" action="{{ route('purchase-orders.destroy', $order) }}" onsubmit="return confirm('Delete this PO?')">@csrf @method('DELETE')
                <button class="w-full rounded-lg border border-rose-200 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50">Delete PO</button>
            </form>
        </div>
    </div>
</x-layouts.app>
