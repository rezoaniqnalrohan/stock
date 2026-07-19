@props(['color' => 'slate'])
@php
    $map = [
        'slate' => 'bg-slate-100 text-slate-600',
        'green' => 'bg-emerald-100 text-emerald-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'red' => 'bg-rose-100 text-rose-700',
        'blue' => 'bg-sky-100 text-sky-700',
        'violet' => 'bg-brand-100 text-brand-700',
    ];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold '.($map[$color] ?? $map['slate'])]) }}>
    {{ $slot }}
</span>
