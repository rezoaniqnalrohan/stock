@props(['label', 'value', 'icon', 'tone' => 'brand', 'delta' => null, 'up' => true])
@php
    $tones = [
        'brand' => 'bg-teal-50 text-teal-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'sky'   => 'bg-sky-50 text-sky-600',
        'violet'=> 'bg-violet-50 text-violet-600',
    ];
@endphp
<div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200/70 shadow-sm">
    <div class="flex items-start justify-between">
        <div class="h-11 w-11 rounded-xl flex items-center justify-center {{ $tones[$tone] ?? $tones['brand'] }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
        </div>
        @if (!is_null($delta))
            <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $up ? 'text-emerald-600' : 'text-rose-500' }}">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $up ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                {{ $delta }}
            </span>
        @endif
    </div>
    <p class="mt-4 text-2xl font-bold text-slate-900 tabular-nums">{{ $value }}</p>
    <p class="text-sm text-slate-400">{{ $label }}</p>
</div>
