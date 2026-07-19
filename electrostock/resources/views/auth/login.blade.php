<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · ElectroStock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: {
            fontFamily: { sans: ['Inter','sans-serif'], display: ['"Space Grotesk"','sans-serif'] },
            colors: { sidebar:'#171712', pill:'#C6D82F', accent:'#AEB818', cream:'#F3F1E7', line:'#ECE9DD', ink:'#1C1C15', muted:'#78786C' }
        }}};
    </script>
</head>
<body class="h-full bg-sidebar font-sans text-ink">
<div class="min-h-full grid lg:grid-cols-2">
    {{-- Brand panel --}}
    <div class="hidden lg:flex flex-col justify-between p-12 text-white">
        <span class="font-display text-xl font-bold tracking-tight">ELECTRO<span class="text-pill">STOCK</span></span>
        <div>
            <h1 class="font-display text-4xl font-bold leading-tight">Run every outlet<br>from one screen.</h1>
            <p class="text-white/50 mt-4 max-w-sm">Real-time stock, transfers, purchasing and point-of-sale for your consumer electronics chain.</p>
        </div>
        <p class="text-white/30 text-xs">© {{ date('Y') }} ElectroStock. Demo build.</p>
    </div>

    {{-- Form panel --}}
    <div class="flex items-center justify-center p-6 bg-cream">
        <div class="w-full max-w-sm">
            <div class="lg:hidden mb-8 text-center font-display text-xl font-bold">ELECTRO<span class="text-accent">STOCK</span></div>
            <h2 class="font-display text-2xl font-bold">Welcome back</h2>
            <p class="text-muted text-sm mt-1 mb-6">Sign in to your dashboard.</p>

            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-2.5 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1.5">Email</label>
                    <input name="email" type="email" value="{{ old('email', 'admin@electrostock.test') }}" required autofocus
                           class="w-full rounded-xl border border-line bg-white px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Password</label>
                    <input name="password" type="password" value="password" required
                           class="w-full rounded-xl border border-line bg-white px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent">
                </div>
                <label class="flex items-center gap-2 text-sm text-muted">
                    <input type="checkbox" name="remember" class="rounded border-line text-accent focus:ring-accent"> Remember me
                </label>
                <button class="w-full rounded-xl bg-sidebar text-white font-semibold py-2.5 text-sm hover:bg-black transition">Sign in</button>
            </form>

            <div class="mt-6 text-xs text-muted bg-white rounded-xl border border-line p-3 space-y-1">
                <p class="font-semibold text-ink">Demo logins (password: <span class="font-mono">password</span>)</p>
                <p>admin@electrostock.test · manager@electrostock.test · cashier@electrostock.test</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
