@php
    $pendingTransfers = \App\Models\Transfer::where('status', 'pending')->count();
    $nav = [
        ['dashboard',        'Home',            'M3 12l9-9 9 9M5 10v10h14V10'],
        ['pos',              'Sell',            'M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6'],
        ['reports',          'Reporting',       'M4 20V10M10 20V4M16 20v-8M20 20H2'],
        ['products.index',   'Catalog',         'M21 16V8a2 2 0 00-1-1.7l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.7l7 4a2 2 0 002 0l7-4A2 2 0 0021 16zM3.3 7L12 12l8.7-5M12 22V12'],
        ['inventory',        'Inventory',       'M20 7H4M20 7l-1 12a2 2 0 01-2 2H7a2 2 0 01-2-2L4 7M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3'],
        ['transfers.index',  'Transfers',       'M8 3L4 7l4 4M4 7h16M16 21l4-4-4-4M20 17H4', $pendingTransfers],
        ['purchase-orders.index', 'Purchase Orders', 'M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0'],
        ['suppliers.index',  'Suppliers',       'M3 21h18M5 21V7l8-4v18M19 21V11l-6-4'],
        ['customers.index',  'Customers',       'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75'],
        ['setup.index',      'Setup',           'M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-2.82 1.17V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06A1.65 1.65 0 004.6 15H4.5a2 2 0 110-4h.09A1.65 1.65 0 006 9.4a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06A1.65 1.65 0 009 4.6h.09A1.65 1.65 0 0011 3.5V3a2 2 0 114 0v.09A1.65 1.65 0 0016 4.6a1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06A1.65 1.65 0 0021 9.4h.5a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1.6z'],
    ];
    $u = auth()->user();
@endphp

<div class="flex items-center gap-2 px-5 h-16 shrink-0">
    <span class="font-display text-lg font-bold tracking-tight text-white">ELECTRO<span class="text-pill">STOCK</span></span>
</div>

<nav class="flex-1 px-3 space-y-1 mt-2">
    @foreach ($nav as $item)
        @php $active = request()->routeIs($item[0]) || (str_contains($item[0], '.') && request()->routeIs(explode('.', $item[0])[0].'.*')); @endphp
        <a href="{{ route($item[0]) }}"
           class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                  {{ $active ? 'bg-sidebar2 text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
            <svg class="w-[18px] h-[18px] {{ $active ? 'text-pill' : '' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $item[2] }}"/></svg>
            <span class="flex-1">{{ $item[1] }}</span>
            @if (($item[3] ?? 0) > 0)
                <span class="text-[11px] font-bold text-sidebar bg-pill rounded-full w-5 h-5 grid place-items-center">{{ $item[3] }}</span>
            @endif
        </a>
    @endforeach
</nav>

<div class="p-3 mt-2">
    <div class="flex items-center gap-3 px-2 py-2 rounded-xl bg-white/5">
        <div class="w-9 h-9 rounded-full bg-pill text-sidebar grid place-items-center font-bold text-sm">
            {{ strtoupper(substr($u->name, 0, 1)) }}
        </div>
        <div class="min-w-0 flex-1">
            <div class="text-sm text-white font-medium truncate">{{ $u->name }}</div>
            <div class="text-[11px] text-white/50 capitalize">{{ $u->role }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-white/50 hover:text-white p-1.5" title="Log out">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
            </button>
        </form>
    </div>
</div>
