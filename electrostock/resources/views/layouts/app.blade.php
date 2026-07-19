<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · ElectroStock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        display: ['"Space Grotesk"', 'Inter', 'sans-serif'],
                    },
                    colors: {
                        cream: '#F3F1E7',
                        card: '#FFFFFF',
                        ink: '#1C1C15',
                        muted: '#78786C',
                        line: '#ECE9DD',
                        sidebar: '#171712',
                        sidebar2: '#22221A',
                        accent: '#AEB818',
                        accentdk: '#8E9612',
                        pill: '#C6D82F',
                        olive: '#9AA23A',
                    },
                },
            },
        };
    </script>
    <style>
        body { -webkit-font-smoothing: antialiased; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-thumb { background: #d8d5c7; border-radius: 8px; }
    </style>
</head>
<body class="h-full bg-cream text-ink font-sans">
<div class="min-h-full lg:flex">
    {{-- Sidebar --}}
    <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-sidebar text-white/80 min-h-screen sticky top-0 self-start">
        @include('layouts.sidebar')
    </aside>

    {{-- Mobile top bar --}}
    <div class="lg:hidden flex items-center justify-between bg-sidebar text-white px-4 py-3">
        <span class="font-display font-bold tracking-tight">ELECTRO<span class="text-pill">STOCK</span></span>
        <details class="relative">
            <summary class="list-none cursor-pointer p-2 -m-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </summary>
            <div class="absolute right-0 mt-2 w-56 bg-sidebar rounded-xl p-2 z-50 shadow-xl">
                @include('layouts.sidebar', ['mobile' => true])
            </div>
        </details>
    </div>

    {{-- Main --}}
    <main class="flex-1 min-w-0 px-5 sm:px-8 py-6 sm:py-8">
        @if (session('status'))
            <div class="mb-5 rounded-xl bg-accent/15 border border-accent/30 text-accentdk px-4 py-3 text-sm font-medium">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-5 rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>
@stack('scripts')
</body>
</html>
