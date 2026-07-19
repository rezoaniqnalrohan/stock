@extends('layouts.app')
@section('title', 'Expiring Soon')
@section('heading', 'Expiring soon')
@section('subheading', 'Batches expiring within the next 30 days — act to avoid wastage')

@section('content')
<x-card>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100">
                    <th class="py-3 px-2 font-semibold">Product</th>
                    <th class="py-3 px-2 font-semibold">Batch</th>
                    <th class="py-3 px-2 font-semibold">Warehouse</th>
                    <th class="py-3 px-2 font-semibold text-right">Qty</th>
                    <th class="py-3 px-2 font-semibold text-right">Expiry date</th>
                    <th class="py-3 px-2 font-semibold text-right">Countdown</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($batches as $b)
                    @php $days = (int) round(now()->startOfDay()->diffInDays($b->expiry_date, false)); @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-2 font-medium text-slate-800">{{ $b->product->name }}</td>
                        <td class="py-3 px-2 font-mono text-xs text-slate-500">{{ $b->batch_no }}</td>
                        <td class="py-3 px-2 text-slate-600">{{ $b->warehouse->name }}</td>
                        <td class="py-3 px-2 text-right tabular-nums font-semibold">{{ number_format($b->quantity) }}</td>
                        <td class="py-3 px-2 text-right text-slate-600 tabular-nums">{{ $b->expiry_date->format('M d, Y') }}</td>
                        <td class="py-3 px-2 text-right">
                            <x-badge :color="$days < 0 ? 'red' : ($days <= 7 ? 'red' : ($days <= 14 ? 'amber' : 'green'))">
                                {{ $days < 0 ? 'Expired '.abs($days).'d ago' : $days.' days' }}
                            </x-badge>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty message="Nothing expiring in the next 30 days. Fresh!" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $batches->links() }}</div>
</x-card>
@endsection
