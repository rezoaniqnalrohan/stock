<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · FreshStock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter','sans-serif'] }, colors: { brand: {50:'#f5f3ff',100:'#ede9fe',200:'#ddd6fe',300:'#c4b5fd',400:'#a78bfa',500:'#8b5cf6',600:'#7c3aed',700:'#6d28d9',800:'#5b21b6',900:'#4c1d95'} } } } }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family:'Inter',sans-serif } </style>
</head>
<body class="h-full bg-slate-100">
<div class="min-h-full grid lg:grid-cols-2">
    {{-- Brand panel --}}
    <div class="hidden lg:flex flex-col justify-between p-12 bg-gradient-to-br from-brand-600 via-brand-700 to-brand-900 text-white">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/15 grid place-items-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 100-4h14a2 2 0 100 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            </div>
            <span class="text-xl font-extrabold tracking-tight">FreshStock</span>
        </div>
        <div>
            <h1 class="text-4xl font-extrabold leading-tight">Fresh goods,<br>flawless flow.</h1>
            <p class="mt-4 text-brand-100 max-w-sm">Cold-chain aware inventory, procurement and logistics for food &amp; beverage distributors. Track batches, beat expiry, never run dry.</p>
            <div class="mt-8 grid grid-cols-3 gap-4 max-w-md">
                <div><p class="text-2xl font-bold">28+</p><p class="text-xs text-brand-200">SKUs tracked</p></div>
                <div><p class="text-2xl font-bold">3</p><p class="text-xs text-brand-200">Warehouses</p></div>
                <div><p class="text-2xl font-bold">FEFO</p><p class="text-xs text-brand-200">Expiry logic</p></div>
            </div>
        </div>
        <p class="text-xs text-brand-200">© {{ date('Y') }} FreshStock Distribution Suite</p>
    </div>

    {{-- Form panel --}}
    <div class="flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-sm">
            <div class="lg:hidden flex items-center gap-2 mb-8">
                <div class="w-9 h-9 rounded-xl bg-brand-600 grid place-items-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 100-4h14a2 2 0 100 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg>
                </div>
                <span class="font-extrabold text-slate-900">FreshStock</span>
            </div>

            <h2 class="text-2xl font-bold text-slate-900">Welcome back</h2>
            <p class="text-sm text-slate-500 mt-1">Sign in to your distribution dashboard.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="/login" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5" for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', 'admin@freshstock.test') }}" required autofocus
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5" for="password">Password</label>
                    <input id="password" name="password" type="password" value="password" required
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none text-sm">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    Remember me
                </label>
                <button class="w-full h-11 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm transition shadow-sm shadow-brand-200">
                    Sign in
                </button>
            </form>

            <div class="mt-6 rounded-xl bg-slate-50 border border-slate-200 p-4 text-xs text-slate-500">
                <p class="font-semibold text-slate-600 mb-1">Demo accounts (password: <code>password</code>)</p>
                <p>admin@freshstock.test · warehouse@freshstock.test · procurement@freshstock.test</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
