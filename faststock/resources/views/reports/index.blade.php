@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<div class="grid gap-5 lg:grid-cols-2">
    {{-- Stock valuation --}}
    <div class="rounded-3xl bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-semibold">Stock valuation</h2>
            <a href="{{ route('reports.export', 'valuation') }}"
               class="flex items-center gap-2 rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 transition hover:border-ink hover:text-ink">
                <x-icon name="download" class="h-4 w-4"/> CSV
            </a>
        </div>
        <div class="max-h-[480px] overflow-auto">
            <table class="w-full text-sm">
                <thead class="sticky top-0 bg-white">
                    <tr class="text-left text-[11px] font-medium uppercase tracking-wide text-gray-400">
                        <th class="pb-3">Ingredient</th><th class="pb-3">Category</th>
                        <th class="pb-3 text-right">Stock</th><th class="pb-3 text-right">Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ingredients as $ingredient)
                        <tr class="border-t border-gray-100">
                            <td class="py-2.5 font-medium">{{ $ingredient->name }}</td>
                            <td class="py-2.5 text-gray-500">{{ $ingredient->category }}</td>
                            <td class="num py-2.5 text-right">{{ $ingredient->stock + 0 }} {{ $ingredient->unit }}</td>
                            <td class="num py-2.5 text-right">৳{{ number_format($ingredient->stock * $ingredient->cost) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-ink font-bold">
                        <td class="py-3" colspan="3">Total stock value</td>
                        <td class="num py-3 text-right">৳{{ number_format($ingredients->sum(fn ($i) => $i->stock * $i->cost)) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Fast / slow movers --}}
    <div class="rounded-3xl bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-semibold">Menu movers <span class="text-sm font-normal text-gray-400">(30 days)</span></h2>
            <a href="{{ route('reports.export', 'movers') }}"
               class="flex items-center gap-2 rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 transition hover:border-ink hover:text-ink">
                <x-icon name="download" class="h-4 w-4"/> CSV
            </a>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-medium uppercase tracking-wide text-gray-400">
                    <th class="pb-3">#</th><th class="pb-3">Item</th><th class="pb-3 text-right">Price</th>
                    <th class="pb-3 text-right">Sold</th><th class="pb-3 text-right">Revenue</th><th class="pb-3 text-right">Pace</th>
                </tr>
            </thead>
            <tbody>
                @php $maxSold = max($movers->max('sold'), 1); @endphp
                @foreach ($movers as $rank => $item)
                    <tr class="border-t border-gray-100">
                        <td class="num py-2.5 text-gray-400">{{ $rank + 1 }}</td>
                        <td class="py-2.5 font-medium">{{ $item->name }}</td>
                        <td class="num py-2.5 text-right">৳{{ number_format($item->price) }}</td>
                        <td class="num py-2.5 text-right font-semibold">{{ $item->sold }}</td>
                        <td class="num py-2.5 text-right">৳{{ number_format($item->sold * $item->price) }}</td>
                        <td class="py-2.5 text-right">
                            @if ($item->sold >= $maxSold * 0.6)
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-[11px] font-semibold text-green-700">Fast</span>
                            @elseif ($item->sold <= $maxSold * 0.2)
                                <span class="rounded-full bg-brand/10 px-2.5 py-1 text-[11px] font-semibold text-brand">Slow</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-semibold text-gray-500">Steady</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p class="mt-4 rounded-xl bg-canvas px-4 py-3 text-xs text-gray-500">
            Movement history export lives on the <a href="{{ route('movements.index') }}" class="font-medium text-brand hover:underline">Stock movements</a> page — filter first, then download.
        </p>
    </div>
</div>
@endsection
