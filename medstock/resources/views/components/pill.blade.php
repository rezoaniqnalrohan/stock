@props(['status'])
@php
    $map = [
        'in'       => ['In Stock',   'bg-emerald-50 text-emerald-700 ring-emerald-600/20'],
        'low'      => ['Low Stock',  'bg-amber-50 text-amber-700 ring-amber-600/20'],
        'out'      => ['Out of Stock','bg-rose-50 text-rose-700 ring-rose-600/20'],
        'expiring' => ['Expiring',   'bg-orange-50 text-orange-700 ring-orange-600/20'],
        // Order statuses
        'draft'    => ['Draft',      'bg-slate-100 text-slate-600 ring-slate-500/20'],
        'ordered'  => ['Ordered',    'bg-sky-50 text-sky-700 ring-sky-600/20'],
        'received' => ['Received',   'bg-emerald-50 text-emerald-700 ring-emerald-600/20'],
        'pending'  => ['Pending',    'bg-slate-100 text-slate-600 ring-slate-500/20'],
        'picked'   => ['Picked',     'bg-sky-50 text-sky-700 ring-sky-600/20'],
        'packed'   => ['Packed',     'bg-violet-50 text-violet-700 ring-violet-600/20'],
        'shipped'  => ['Shipped',    'bg-emerald-50 text-emerald-700 ring-emerald-600/20'],
    ];
    [$label, $classes] = $map[$status] ?? [ucfirst($status), 'bg-slate-100 text-slate-600 ring-slate-500/20'];
@endphp
<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $classes }}">{{ $label }}</span>
