@extends('layouts.app')
@section('title', 'Shipments')
@section('heading', 'Logistics')
@section('subheading', 'Inbound, outbound and inter-warehouse movements')

@section('actions')
    <a href="/shipments/create" class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium shadow-sm shadow-brand-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
        New shipment
    </a>
@endsection

@section('content')
<x-card>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100">
                    <th class="py-3 px-2 font-semibold">Reference</th>
                    <th class="py-3 px-2 font-semibold">Type</th>
                    <th class="py-3 px-2 font-semibold">Origin</th>
                    <th class="py-3 px-2 font-semibold">Destination</th>
                    <th class="py-3 px-2 font-semibold">Ship date</th>
                    <th class="py-3 px-2 font-semibold text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($shipments as $s)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-2 font-semibold text-slate-800">{{ $s->reference }}</td>
                        <td class="py-3 px-2">
                            <x-badge :color="['inbound' => 'green', 'outbound' => 'blue', 'transfer' => 'violet'][$s->type]">{{ ucfirst($s->type) }}</x-badge>
                        </td>
                        <td class="py-3 px-2 text-slate-600">{{ $s->origin ?? '—' }}</td>
                        <td class="py-3 px-2 text-slate-600">{{ $s->destination ?? '—' }}</td>
                        <td class="py-3 px-2 text-slate-500 tabular-nums">{{ $s->ship_date?->format('M d, Y') ?? '—' }}</td>
                        <td class="py-3 px-2 text-right">
                            {{-- Inline status update: submits on change --}}
                            <form method="POST" action="/shipments/{{ $s->id }}/status" class="inline">
                                @csrf
                                <select name="status" onchange="this.form.submit()"
                                        class="h-8 pl-2 pr-7 rounded-lg border border-slate-200 text-xs font-semibold focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none
                                               {{ ['pending' => 'text-amber-700 bg-amber-50', 'in_transit' => 'text-sky-700 bg-sky-50', 'delivered' => 'text-emerald-700 bg-emerald-50'][$s->status] }}">
                                    @foreach (['pending' => 'Pending', 'in_transit' => 'In transit', 'delivered' => 'Delivered'] as $v => $t)
                                        <option value="{{ $v }}" @selected($s->status === $v)>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty message="No shipments recorded." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $shipments->links() }}</div>
</x-card>
@endsection
