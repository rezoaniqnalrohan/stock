@extends('layouts.app')
@section('title', 'Sales')
@section('content')
<div class="rounded-3xl bg-white p-6 shadow-sm">
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-gray-500">Every sale automatically deducts recipe ingredients from stock.</p>
        <a href="{{ route('sales.create') }}"
           class="flex shrink-0 items-center gap-2 rounded-full bg-brand px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand/30 transition hover:bg-orange-600">
            <x-icon name="plus" class="h-4 w-4"/> New sale
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-medium uppercase tracking-wide text-gray-400">
                    <th class="pb-3">Sale</th><th class="pb-3">Date</th><th class="pb-3">Items</th><th class="pb-3 text-right">Total</th><th class="pb-3">By</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr class="border-t border-gray-100">
                        <td class="num py-3 font-semibold">#{{ $sale->id }}</td>
                        <td class="num py-3 text-gray-500">{{ $sale->created_at->format('d M Y, H:i') }}</td>
                        <td class="max-w-96 truncate py-3">{{ $sale->items->map(fn ($i) => $i->qty.'× '.$i->menuItem->name)->join(', ') }}</td>
                        <td class="num py-3 text-right font-semibold">৳{{ number_format($sale->total) }}</td>
                        <td class="py-3 text-gray-500">{{ $sale->user->name }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-10 text-center text-gray-400">No sales recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $sales->links() }}</div>
</div>
@endsection
