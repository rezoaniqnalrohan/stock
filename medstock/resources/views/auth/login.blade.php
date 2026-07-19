<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · MedStock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body{font-family:Inter,system-ui,sans-serif}</style>
</head>
<body class="h-full bg-slate-50">
<div class="min-h-full grid lg:grid-cols-2">
    {{-- Brand panel --}}
    <div class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-teal-600 to-teal-800 p-12 text-white">
        <div class="flex items-center gap-2.5">
            <div class="h-10 w-10 rounded-xl bg-white/15 flex items-center justify-center">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            <span class="text-xl font-bold">MedStock</span>
        </div>
        <div>
            <h1 class="text-4xl font-bold leading-tight">Medical &amp; PPE<br>inventory, under control.</h1>
            <p class="mt-4 text-teal-100 max-w-md">Track masks, gloves, gowns, syringes and diagnostic kits across warehouses — with batch, lot and expiry visibility built for distributors.</p>
        </div>
        <div class="flex gap-8 text-sm text-teal-100">
            <div><p class="text-2xl font-bold text-white">15+</p>SKUs seeded</div>
            <div><p class="text-2xl font-bold text-white">2</p>Warehouses</div>
            <div><p class="text-2xl font-bold text-white">FEFO</p>Expiry-aware</div>
        </div>
    </div>

    {{-- Form --}}
    <div class="flex items-center justify-center p-6">
        <div class="w-full max-w-sm">
            <div class="lg:hidden flex items-center gap-2.5 mb-8">
                <div class="h-10 w-10 rounded-xl bg-teal-600 flex items-center justify-center text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </div>
                <span class="text-xl font-bold text-slate-900">MedStock</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900">Welcome back</h2>
            <p class="mt-1 text-sm text-slate-500">Sign in to your distribution dashboard.</p>

            @if ($errors->any())
                <div class="mt-4 rounded-lg bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', 'admin@medstock.test') }}" required autofocus
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" value="password" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                    Remember me
                </label>
                <button type="submit" class="w-full rounded-lg bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-700 transition shadow-sm">Sign in</button>
            </form>

            <div class="mt-6 rounded-lg bg-slate-50 border border-slate-200 p-4 text-xs text-slate-500">
                <p class="font-semibold text-slate-600 mb-1">Demo logins (password: <span class="font-mono">password</span>)</p>
                <p>admin@medstock.test · manager@medstock.test · sales@medstock.test</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
