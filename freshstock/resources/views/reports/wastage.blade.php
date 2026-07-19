@extends('layouts.app')
@section('title', 'Wastage')
@section('heading', 'Wastage report')
@section('subheading', 'Negative adjustments — spoilage, damage and write-offs')

@section('content')
@include('reports._tabs')

<div class="grid sm:grid-cols-2 gap-4 mb-4">
    <x-card>
        <p class="text-sm text-slate-500">Units written off</p>
        <p class="text-2xl font-extrabold text-rose-600 mt-1">{{ number_format($totalUnits) }}</p>
    </x-card>
    <x-card>
        <p class="text-sm text-slate-500">Write-off events</p>
        <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($rows->total()) }}</p>
    </x-card>
</div>

<x-card title="Write-off log">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100">
                    <th class="py-3 px-2 font-semibold">Date</th>
                    <th class="py-3 px-2 font-semibold">Product</th>
                    <th class="py-3 px-2 font-semibold">Warehouse</th>
                    <th class="py-3 px-2 font-semibold">Reason</th>
                    <th class="py-3 px-2 font-semibold text-right">Units</th>
                    <th class="py-3 px-2 font-semibold text-right">Cost impact</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($rows as $r)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-2 text-slate-500 tabular-nums whitespace-nowrap">{{ $r->created_at->format('M d, Y') }}</td>
                        <td class="py-3 px-2 font-medium text-slate-800">{{ $r->product->name }}</td>
                        <td class="py-3 px-2 text-slate-500">{{ $r->warehouse->code }}</td>
                        <td class="py-3 px-2 text-slate-500 text-xs">{{ $r->note ?? '—' }}</td>
                        <td class="py-3 px-2 text-right tabular-nums font-semibold text-rose-600">{{ number_format($r->quantity) }}</td>
                        <td class="py-3 px-2 text-right tabular-nums text-slate-600">${{ number_format(abs($r->quantity) * $r->product->cost, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty message="No wastage recorded. Nothing spoiled." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $rows->links() }}</div>
</x-card>
@endsection
