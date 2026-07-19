@extends('layouts.app')
@section('title', $order->po_number)
@section('heading', $order->po_number)
@section('subheading', $order->supplier->name.' → '.$order->warehouse->name)

@section('actions')
    @if ($order->status !== 'received')
        <form method="POST" action="/purchase-orders/{{ $order->id }}/receive" onsubmit="return confirm('Receive all items into stock?')">
            @csrf
            <button class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Receive goods
            </button>
        </form>
    @endif
@endsection

@section('content')
<div class="grid lg:grid-cols-3 gap-4">
    <x-card title="Order info" class="lg:col-span-1">
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd><x-badge :color="['draft' => 'slate', 'ordered' => 'amber', 'received' => 'green'][$order->status]">{{ ucfirst($order->status) }}</x-badge></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Supplier</dt><dd class="font-medium text-slate-800">{{ $order->supplier->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Warehouse</dt><dd class="font-medium text-slate-800">{{ $order->warehouse->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Order date</dt><dd class="text-slate-700">{{ $order->order_date->format('M d, Y') }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Expected</dt><dd class="text-slate-700">{{ $order->expected_date?->format('M d, Y') ?? '—' }}</dd></div>
            <div class="flex justify-between border-t border-slate-100 pt-3"><dt class="text-slate-500 font-medium">Total</dt><dd class="font-bold text-slate-900">${{ number_format($order->total, 2) }}</dd></div>
        </dl>
    </x-card>

    <x-card title="Items" class="lg:col-span-2">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100">
                        <th class="py-2 px-2 font-semibold">Product</th>
                        <th class="py-2 px-2 font-semibold text-right">Qty</th>
                        <th class="py-2 px-2 font-semibold text-right">Unit cost</th>
                        <th class="py-2 px-2 font-semibold text-right">Line total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($order->items as $it)
                        <tr>
                            <td class="py-2.5 px-2 font-medium text-slate-800">{{ $it->product->name }}</td>
                            <td class="py-2.5 px-2 text-right tabular-nums">{{ number_format($it->quantity) }}</td>
                            <td class="py-2.5 px-2 text-right tabular-nums">${{ number_format($it->unit_cost, 2) }}</td>
                            <td class="py-2.5 px-2 text-right tabular-nums font-semibold">${{ number_format($it->quantity * $it->unit_cost, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
