@extends('layouts.app')
@section('title', $product->name)
@section('heading', $product->name)
@section('subheading', $product->sku.' · '.$product->category->name)

@section('actions')
    <a href="/products/{{ $product->id }}/edit" class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium shadow-sm shadow-brand-200">Edit</a>
@endsection

@section('content')
@php $temp = ['ambient' => 'slate', 'chilled' => 'blue', 'frozen' => 'violet'][$product->storage_temp]; @endphp
<div class="grid lg:grid-cols-3 gap-4">
    <x-card title="Details" class="lg:col-span-1">
        <dl class="space-y-3 text-sm">
            @foreach ([
                'Unit' => $product->unit->name,
                'Supplier' => $product->supplier?->name ?? '—',
                'Sell price' => '$'.number_format($product->price, 2),
                'Cost' => '$'.number_format($product->cost, 2),
                'Shelf life' => $product->shelf_life_days.' days',
                'Reorder point' => number_format($product->reorder_point).' units',
            ] as $k => $v)
                <div class="flex justify-between"><dt class="text-slate-500">{{ $k }}</dt><dd class="font-medium text-slate-800">{{ $v }}</dd></div>
            @endforeach
            <div class="flex justify-between items-center"><dt class="text-slate-500">Storage</dt><dd><x-badge :color="$temp">{{ ucfirst($product->storage_temp) }}</x-badge></dd></div>
        </dl>
    </x-card>

    <x-card title="Batches on hand" subtitle="{{ number_format($product->stockOnHand()) }} units total · valued ${{ number_format($product->stockValue(), 2) }}" class="lg:col-span-2">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100">
                        <th class="py-2 px-2 font-semibold">Batch</th>
                        <th class="py-2 px-2 font-semibold">Warehouse</th>
                        <th class="py-2 px-2 font-semibold text-right">Qty</th>
                        <th class="py-2 px-2 font-semibold text-right">Expiry</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($product->batches->sortBy('expiry_date') as $b)
                        @php $days = $b->expiry_date ? (int) round(now()->startOfDay()->diffInDays($b->expiry_date, false)) : null; @endphp
                        <tr>
                            <td class="py-2.5 px-2 font-mono text-xs text-slate-500">{{ $b->batch_no }}</td>
                            <td class="py-2.5 px-2 text-slate-600">{{ $b->warehouse->name }}</td>
                            <td class="py-2.5 px-2 text-right tabular-nums font-semibold">{{ number_format($b->quantity) }}</td>
                            <td class="py-2.5 px-2 text-right">
                                @if ($b->expiry_date)
                                    <span class="text-slate-600">{{ $b->expiry_date->format('M d, Y') }}</span>
                                    <x-badge :color="$days < 0 ? 'red' : ($days <= 14 ? 'amber' : 'green')" class="ml-1">{{ $days < 0 ? 'Expired' : $days.'d' }}</x-badge>
                                @else <span class="text-slate-400">—</span> @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><x-empty message="No stock batches for this product." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
