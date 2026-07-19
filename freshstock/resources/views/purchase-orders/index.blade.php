@extends('layouts.app')
@section('title', 'Purchase Orders')
@section('heading', 'Purchase orders')
@section('subheading', 'Procurement and goods receiving')

@section('actions')
    <a href="/purchase-orders/create" class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium shadow-sm shadow-brand-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
        New PO
    </a>
@endsection

@section('content')
<x-card>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100">
                    <th class="py-3 px-2 font-semibold">PO #</th>
                    <th class="py-3 px-2 font-semibold">Supplier</th>
                    <th class="py-3 px-2 font-semibold">Warehouse</th>
                    <th class="py-3 px-2 font-semibold">Ordered</th>
                    <th class="py-3 px-2 font-semibold text-right">Total</th>
                    <th class="py-3 px-2 font-semibold text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($orders as $o)
                    <tr class="hover:bg-slate-50 cursor-pointer" onclick="location='/purchase-orders/{{ $o->id }}'">
                        <td class="py-3 px-2 font-semibold text-brand-600">{{ $o->po_number }}</td>
                        <td class="py-3 px-2 text-slate-700">{{ $o->supplier->name }}</td>
                        <td class="py-3 px-2 text-slate-500">{{ $o->warehouse->code }}</td>
                        <td class="py-3 px-2 text-slate-500 tabular-nums">{{ $o->order_date->format('M d, Y') }}</td>
                        <td class="py-3 px-2 text-right tabular-nums font-semibold">${{ number_format($o->total, 2) }}</td>
                        <td class="py-3 px-2 text-center">
                            <x-badge :color="['draft' => 'slate', 'ordered' => 'amber', 'received' => 'green'][$o->status]">{{ ucfirst($o->status) }}</x-badge>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty message="No purchase orders yet." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
</x-card>
@endsection
