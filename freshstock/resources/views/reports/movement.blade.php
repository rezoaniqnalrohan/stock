@extends('layouts.app')
@section('title', 'Movement History')
@section('heading', 'Movement history')
@section('subheading', 'Every stock in, out, adjustment and transfer')

@section('content')
@include('reports._tabs')

<x-card>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100">
                    <th class="py-3 px-2 font-semibold">Date</th>
                    <th class="py-3 px-2 font-semibold">Product</th>
                    <th class="py-3 px-2 font-semibold">Warehouse</th>
                    <th class="py-3 px-2 font-semibold">Type</th>
                    <th class="py-3 px-2 font-semibold">Reference</th>
                    <th class="py-3 px-2 font-semibold">Note</th>
                    <th class="py-3 px-2 font-semibold text-right">Qty</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($movements as $m)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-2 text-slate-500 tabular-nums whitespace-nowrap">{{ $m->created_at->format('M d, Y') }}</td>
                        <td class="py-3 px-2 font-medium text-slate-800">{{ $m->product->name }}</td>
                        <td class="py-3 px-2 text-slate-500">{{ $m->warehouse->code }}</td>
                        <td class="py-3 px-2">
                            <x-badge :color="['in' => 'green', 'out' => 'blue', 'adjustment' => 'amber', 'transfer' => 'violet'][$m->type]">{{ ucfirst($m->type) }}</x-badge>
                        </td>
                        <td class="py-3 px-2 font-mono text-xs text-slate-500">{{ $m->reference ?? '—' }}</td>
                        <td class="py-3 px-2 text-slate-500 text-xs">{{ $m->note ?? '—' }}</td>
                        <td class="py-3 px-2 text-right tabular-nums font-semibold {{ $m->quantity < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $m->quantity > 0 ? '+' : '' }}{{ number_format($m->quantity) }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><x-empty message="No stock movements recorded." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $movements->links() }}</div>
</x-card>
@endsection
