@extends('layouts.app')
@section('title', 'Expiring Stock')
@section('heading', 'Expiring stock report')
@section('subheading', 'Every batch with an expiry date, soonest first')

@section('content')
@include('reports._tabs')

<x-card>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100">
                    <th class="py-3 px-2 font-semibold">Product</th>
                    <th class="py-3 px-2 font-semibold">Batch</th>
                    <th class="py-3 px-2 font-semibold">Warehouse</th>
                    <th class="py-3 px-2 font-semibold text-right">Qty</th>
                    <th class="py-3 px-2 font-semibold text-right">Value at risk</th>
                    <th class="py-3 px-2 font-semibold text-right">Expiry</th>
                    <th class="py-3 px-2 font-semibold text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($batches as $b)
                    @php $days = (int) round(now()->startOfDay()->diffInDays($b->expiry_date, false)); @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-2 font-medium text-slate-800">{{ $b->product->name }}</td>
                        <td class="py-3 px-2 font-mono text-xs text-slate-500">{{ $b->batch_no }}</td>
                        <td class="py-3 px-2 text-slate-500">{{ $b->warehouse->code }}</td>
                        <td class="py-3 px-2 text-right tabular-nums">{{ number_format($b->quantity) }}</td>
                        <td class="py-3 px-2 text-right tabular-nums text-slate-600">${{ number_format($b->quantity * $b->product->cost, 2) }}</td>
                        <td class="py-3 px-2 text-right text-slate-600 tabular-nums whitespace-nowrap">{{ $b->expiry_date->format('M d, Y') }}</td>
                        <td class="py-3 px-2 text-right">
                            <x-badge :color="$days < 0 ? 'red' : ($days <= 7 ? 'red' : ($days <= 30 ? 'amber' : 'green'))">
                                {{ $days < 0 ? 'Expired' : $days.'d left' }}
                            </x-badge>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><x-empty message="No batches with expiry dates." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $batches->links() }}</div>
</x-card>
@endsection
