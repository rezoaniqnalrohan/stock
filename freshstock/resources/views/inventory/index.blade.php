@extends('layouts.app')
@section('title', 'Inventory')
@section('heading', 'Inventory levels')
@section('subheading', 'Stock on hand across every warehouse')

@section('actions')
    <a href="/inventory/transfer" class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-white text-slate-700 text-sm font-medium shadow-sm hover:bg-slate-50">Transfer</a>
    <a href="/inventory/adjust" class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium shadow-sm shadow-brand-200">Adjust stock</a>
@endsection

@section('content')
<x-card>
    <form method="GET" class="flex items-center gap-3 mb-4 max-w-md">
        <div class="relative flex-1">
            <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M21 21l-4.3-4.3M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
            <input name="search" value="{{ request('search') }}" placeholder="Search product…" class="w-full h-10 pl-9 pr-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none text-sm">
        </div>
        <button class="h-10 px-4 rounded-xl bg-slate-800 text-white text-sm font-medium">Search</button>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100">
                    <th class="py-3 px-2 font-semibold">Product</th>
                    @foreach ($warehouses as $w)<th class="py-3 px-2 font-semibold text-right">{{ $w->code }}</th>@endforeach
                    <th class="py-3 px-2 font-semibold text-right">Total</th>
                    <th class="py-3 px-2 font-semibold text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($products as $p)
                    @php $total = $p->batches->sum('quantity'); @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-2">
                            <a href="/products/{{ $p->id }}" class="font-medium text-slate-800 hover:text-brand-600">{{ $p->name }}</a>
                            <span class="block text-xs text-slate-400">{{ $p->sku }}</span>
                        </td>
                        @foreach ($warehouses as $w)
                            @php $q = $p->batches->where('warehouse_id', $w->id)->sum('quantity'); @endphp
                            <td class="py-3 px-2 text-right tabular-nums {{ $q == 0 ? 'text-slate-300' : 'text-slate-700' }}">{{ number_format($q) }}</td>
                        @endforeach
                        <td class="py-3 px-2 text-right tabular-nums font-bold text-slate-900">{{ number_format($total) }}</td>
                        <td class="py-3 px-2 text-right">
                            @if ($total <= $p->reorder_point)
                                <x-badge color="red">Reorder</x-badge>
                            @elseif ($total <= $p->reorder_point * 1.3)
                                <x-badge color="amber">Low</x-badge>
                            @else
                                <x-badge color="green">In stock</x-badge>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ $warehouses->count() + 3 }}"><x-empty message="No products found." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</x-card>
@endsection
