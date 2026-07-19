<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} · MedStock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: { brand: { DEFAULT: '#0d9488', dark: '#0f766e', light: '#5eead4' } },
                }
            }
        }
    </script>
    <style>
        body { background: #f4f7f9; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
        [x-cloak] { display: none; }
    </style>
</head>
<body class="h-full font-sans text-slate-700 antialiased">
<div class="flex min-h-full">
    {{-- Sidebar --}}
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full bg-white border-r border-slate-200 flex flex-col transition-transform lg:translate-x-0">
        <div class="h-16 flex items-center gap-2.5 px-6 border-b border-slate-100">
            <div class="h-9 w-9 rounded-xl bg-brand flex items-center justify-center text-white shadow-sm">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <p class="font-bold text-slate-900 leading-none">MedStock</p>
                <p class="text-[11px] text-slate-400 mt-0.5">PPE Distribution</p>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 text-sm">
            @php
                $groups = [
                    'Overview' => [
                        ['dashboard', 'Dashboard', 'M3 12l9-9 9 9M5 10v10h14V10'],
                    ],
                    'Catalog' => [
                        ['products.index', 'Products', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14L4 7m8 4v10M4 7v10l8 4'],
                        ['inventory.index', 'Inventory', 'M4 6h16M4 12h16M4 18h16'],
                    ],
                    'Purchasing' => [
                        ['suppliers.index', 'Suppliers', 'M3 7h18M3 7l2 13h14l2-13M8 7V4h8v3'],
                        ['purchase-orders.index', 'Purchase Orders', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2'],
                    ],
                    'Sales' => [
                        ['customers.index', 'Customers', 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z'],
                        ['sales-orders.index', 'Sales Orders', 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 5h12'],
                    ],
                    'Reports' => [
                        ['reports.valuation', 'Stock Valuation', 'M9 19v-6a2 2 0 00-2-2H5m14 8v-3m-4 3V9m-4 10V5'],
                        ['reports.movement', 'Stock Movement', 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
                        ['reports.expiring', 'Expiring Soon', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['reports.sales', 'Sales Report', 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z'],
                    ],
                    'System' => [
                        ['settings.index', 'Settings', 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                    ],
                ];
            @endphp
            @foreach ($groups as $label => $items)
                <div>
                    <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ $label }}</p>
                    <div class="space-y-1">
                        @foreach ($items as [$route, $name, $icon])
                            @php $active = request()->routeIs($route) || request()->routeIs(str_replace('.index', '', $route).'.*'); @endphp
                            <a href="{{ route($route) }}" class="flex items-center gap-3 rounded-lg px-3 py-2 font-medium transition {{ $active ? 'bg-brand text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                                <svg class="h-[18px] w-[18px] {{ $active ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                                {{ $name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>
        <div class="border-t border-slate-100 p-4">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-full bg-brand-dark text-white flex items-center justify-center text-sm font-semibold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-slate-400">{{ auth()->user()->roleLabel() }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="text-slate-400 hover:text-rose-500" title="Log out">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <header class="sticky top-0 z-30 h-16 bg-white/80 backdrop-blur border-b border-slate-200 flex items-center gap-4 px-4 sm:px-6">
            <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="lg:hidden text-slate-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex-1">
                <h1 class="text-lg font-bold text-slate-900">{{ $title ?? 'Dashboard' }}</h1>
                @isset($subtitle)<p class="text-xs text-slate-400">{{ $subtitle }}</p>@endisset
            </div>
            <div class="hidden sm:flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-sm text-slate-400 w-64">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                Search here...
            </div>
            @isset($action){{ $action }}@endisset
        </header>

        <main class="flex-1 p-4 sm:p-6">
            @if (session('status'))
                <div class="mb-4 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
