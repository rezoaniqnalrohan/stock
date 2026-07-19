@php
    // key => [label, url, svg path d]
    $nav = [
        ['Dashboard', '/dashboard', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['Products', '/products', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['Inventory', '/inventory', 'M4 7v10c0 .6.4 1 1 1h14c.6 0 1-.4 1-1V7M4 7c0-.6.4-1 1-1h14c.6 0 1 .4 1 1M4 7l8 5 8-5'],
        ['Expiring Soon', '/inventory/expiring', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['Suppliers', '/suppliers', 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 2.3M17 13l1.3 2.3M9 20a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z'],
        ['Purchase Orders', '/purchase-orders', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
        ['Customers', '/customers', 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z'],
        ['Sales Orders', '/orders', 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 2.3c-.6.6-.2 1.7.7 1.7H17'],
        ['Shipments', '/shipments', 'M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1h4m0 0V9.5a1 1 0 00-.3-.7l-2.5-2.5a1 1 0 00-.7-.3H13'],
        ['Reports', '/reports/valuation', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
    ];
    if (auth()->user()->isAdmin()) {
        $nav[] = ['Settings', '/settings', 'M10.3 4.3a2 2 0 013.4 0l.4.7a2 2 0 002 1 2 2 0 012.4 2.4 2 2 0 001 2l.7.4a2 2 0 010 3.4l-.7.4a2 2 0 00-1 2 2 2 0 01-2.4 2.4 2 2 0 00-2 1l-.4.7a2 2 0 01-3.4 0l-.4-.7a2 2 0 00-2-1 2 2 0 01-2.4-2.4 2 2 0 00-1-2l-.7-.4a2 2 0 010-3.4l.7-.4a2 2 0 001-2A2 2 0 017.9 6a2 2 0 002-1zM12 15a3 3 0 100-6 3 3 0 000 6z'];
    }
    $path = '/'.request()->path();
@endphp

<aside id="sidebar"
       class="fixed lg:sticky top-0 left-0 z-40 h-screen w-64 shrink-0 -translate-x-full lg:translate-x-0 transition-transform duration-200">
    <div class="h-full m-3 lg:mr-0 bg-white rounded-2xl shadow-sm flex flex-col overflow-hidden">
        <div class="flex items-center gap-2 px-5 py-5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 grid place-items-center text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 100-4h14a2 2 0 100 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            </div>
            <div class="leading-tight">
                <p class="font-extrabold text-slate-900 tracking-tight">FreshStock</p>
                <p class="text-[11px] text-slate-400">Distribution Suite</p>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 pb-4 space-y-1">
            @foreach ($nav as [$label, $url, $d])
                @php $active = $path === $url || ($url !== '/dashboard' && str_starts_with($path, $url)); @endphp
                <a href="{{ $url }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                          {{ $active ? 'bg-brand-600 text-white shadow-sm shadow-brand-200' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $d }}"/></svg>
                    <span>{{ $label }}</span>
                </a>
            @endforeach
        </nav>

        <div class="p-3 border-t border-slate-100">
            <form method="POST" action="/logout">
                @csrf
                <button class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-rose-50 hover:text-rose-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sign out
                </button>
            </form>
        </div>
    </div>
</aside>
