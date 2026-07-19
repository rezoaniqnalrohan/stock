<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — FastStock</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            colors: { brand: '#F4470B', ink: '#191919', canvas: '#F1F2F4' },
            fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
        }}};
    </script>
    <style>.num{font-variant-numeric:tabular-nums}</style>
</head>
<body class="h-full bg-canvas text-ink font-sans antialiased">
<div class="flex min-h-full">
    <aside class="fixed inset-y-0 left-0 z-40 flex w-[76px] flex-col items-center gap-2 overflow-y-auto py-5" aria-label="Main navigation">
        <a href="{{ route('dashboard') }}" class="mb-3 grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-brand text-lg font-extrabold text-white shadow-lg shadow-brand/30">F</a>
        @php
            $nav = [
                ['dashboard', 'dashboard', 'Dashboard'],
                ['sales.create', 'cart', 'New sale (POS)'],
                ['ingredients.index', 'package', 'Ingredients'],
                ['menu-items.index', 'utensils', 'Menu items'],
                ['purchases.index', 'truck', 'Purchases'],
                ['waste.index', 'trash', 'Waste'],
                ['movements.index', 'history', 'Stock movements'],
                ['reports.index', 'chart', 'Reports'],
            ];
            if (auth()->user()?->role === 'admin') {
                $nav[] = ['suppliers.index', 'users', 'Suppliers'];
                $nav[] = ['users.index', 'shield', 'Team'];
            }
        @endphp
        @foreach ($nav as [$route, $icon, $label])
            @php $active = $route === 'dashboard' ? request()->routeIs('dashboard') : request()->routeIs(explode('.', $route)[0].'.*'); @endphp
            <a href="{{ route($route) }}" aria-label="{{ $label }}" @if($active) aria-current="page" @endif
               class="group relative grid h-11 w-11 shrink-0 place-items-center rounded-2xl transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand
                      {{ $active ? 'bg-ink text-white shadow-md' : 'bg-white text-gray-400 shadow-sm hover:text-ink' }}">
                <x-icon :name="$icon" class="h-5 w-5"/>
                <span class="pointer-events-none absolute left-14 z-50 hidden whitespace-nowrap rounded-lg bg-ink px-2.5 py-1.5 text-xs font-medium text-white group-hover:block group-focus-visible:block">{{ $label }}</span>
            </a>
        @endforeach
        <form method="POST" action="{{ route('logout') }}" class="mt-auto shrink-0">
            @csrf
            <button aria-label="Log out"
                    class="group relative grid h-11 w-11 cursor-pointer place-items-center rounded-2xl bg-white text-gray-400 shadow-sm transition hover:text-brand focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand">
                <x-icon name="logout" class="h-5 w-5"/>
                <span class="pointer-events-none absolute bottom-1 left-14 hidden whitespace-nowrap rounded-lg bg-ink px-2.5 py-1.5 text-xs font-medium text-white group-hover:block">Log out</span>
            </button>
        </form>
    </aside>

    <main class="ml-[76px] flex-1 px-4 pb-10 pt-6 lg:px-8">
        <div class="mx-auto max-w-[1280px]">
            <header class="mb-6 flex flex-wrap items-center gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">@yield('title', 'Dashboard')</h1>
                    <p class="text-sm text-gray-500">{{ now()->format('l, d M Y') }}</p>
                </div>
                <div class="ms-auto flex items-center gap-2">
                    <span class="rounded-full bg-white px-3.5 py-2 text-sm font-semibold shadow-sm">৳ BDT</span>
                    <span class="flex items-center gap-2 rounded-full bg-white py-1.5 pl-1.5 pr-4 shadow-sm">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-ink text-sm font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="text-sm font-semibold">{{ auth()->user()->name }}</span>
                        <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold uppercase {{ auth()->user()->role === 'admin' ? 'bg-brand/10 text-brand' : 'bg-gray-100 text-gray-500' }}">{{ auth()->user()->role }}</span>
                    </span>
                </div>
            </header>

            @if (session('ok'))
                <div id="flash" role="status" aria-live="polite" class="mb-4 flex items-center gap-2 rounded-2xl bg-ink px-4 py-3 text-sm font-medium text-white">
                    <x-icon name="check" class="h-4 w-4 text-green-400"/> {{ session('ok') }}
                </div>
                <script>setTimeout(() => document.getElementById('flash')?.remove(), 4000)</script>
            @endif
            @if ($errors->any())
                <div role="alert" class="mb-4 rounded-2xl border border-brand/20 bg-brand/5 px-4 py-3 text-sm font-medium text-brand">
                    <ul class="list-inside list-disc space-y-0.5">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>
@stack('scripts')
</body>
</html>
