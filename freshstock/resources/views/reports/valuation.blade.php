@extends('layouts.app')
@section('title', 'Stock Valuation')
@section('heading', 'Stock valuation')
@section('subheading', 'Inventory value by category, at cost and retail')

@section('actions')
    <button onclick="window.print()" class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium shadow-sm shadow-brand-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export
    </button>
@endsection

@section('content')
@include('reports._tabs')

<div class="grid sm:grid-cols-3 gap-4 mb-4">
    <x-card>
        <p class="text-sm text-slate-500">Total value at cost</p>
        <p class="text-2xl font-extrabold text-slate-900 mt-1">${{ number_format($total, 2) }}</p>
    </x-card>
    <x-card>
        <p class="text-sm text-slate-500">Value at retail</p>
        <p class="text-2xl font-extrabold text-slate-900 mt-1">${{ number_format($retail, 2) }}</p>
    </x-card>
    <x-card>
        <p class="text-sm text-slate-500">Potential margin</p>
        <p class="text-2xl font-extrabold text-emerald-600 mt-1">${{ number_format($retail - $total, 2) }}</p>
    </x-card>
</div>

<x-card title="By category">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100">
                    <th class="py-3 px-2 font-semibold">Category</th>
                    <th class="py-3 px-2 font-semibold text-right">Units</th>
                    <th class="py-3 px-2 font-semibold text-right">Value at cost</th>
                    <th class="py-3 px-2 font-semibold text-right">Value at retail</th>
                    <th class="py-3 px-2 font-semibold text-right">Share</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($rows as $r)
                    @php $share = $total > 0 ? $r->cost_val / $total * 100 : 0; @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-2 font-medium text-slate-800">{{ $r->category }}</td>
                        <td class="py-3 px-2 text-right tabular-nums text-slate-600">{{ number_format($r->qty) }}</td>
                        <td class="py-3 px-2 text-right tabular-nums font-semibold">${{ number_format($r->cost_val, 2) }}</td>
                        <td class="py-3 px-2 text-right tabular-nums text-slate-600">${{ number_format($r->retail_val, 2) }}</td>
                        <td class="py-3 px-2 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <div class="w-20 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full bg-brand-600 rounded-full" style="width: {{ round($share) }}%"></div>
                                </div>
                                <span class="tabular-nums text-xs text-slate-500 w-10 text-right">{{ number_format($share, 1) }}%</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-slate-100 font-bold">
                    <td class="py-3 px-2 text-slate-900">Total</td>
                    <td class="py-3 px-2 text-right tabular-nums">{{ number_format($rows->sum('qty')) }}</td>
                    <td class="py-3 px-2 text-right tabular-nums">${{ number_format($total, 2) }}</td>
                    <td class="py-3 px-2 text-right tabular-nums">${{ number_format($retail, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</x-card>
@endsection
