<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — FastStock</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            colors: { brand: '#F4470B', ink: '#191919', canvas: '#F1F2F4' },
            fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
        }}};
    </script>
</head>
<body class="grid min-h-full place-items-center bg-canvas p-4 font-sans text-ink antialiased">
<main class="w-full max-w-sm">
    <div class="rounded-3xl bg-white p-8 shadow-sm">
        <div class="mb-6 flex items-center gap-3">
            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-brand text-lg font-extrabold text-white shadow-lg shadow-brand/30">F</span>
            <div>
                <p class="text-lg font-bold leading-tight">FastStock</p>
                <p class="text-sm text-gray-500">Restaurant stock management</p>
            </div>
        </div>

        @if ($errors->any())
            <div role="alert" class="mb-4 rounded-2xl border border-brand/20 bg-brand/5 px-4 py-3 text-sm font-medium text-brand">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                       class="w-full rounded-xl border border-gray-200 bg-canvas px-4 py-3 text-sm outline-none transition focus:border-ink focus:bg-white">
            </div>
            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="w-full rounded-xl border border-gray-200 bg-canvas px-4 py-3 text-sm outline-none transition focus:border-ink focus:bg-white">
            </div>
            <button class="w-full cursor-pointer rounded-xl bg-ink py-3 text-sm font-semibold text-white transition hover:bg-black focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand">
                Sign in
            </button>
        </form>
    </div>
    <p class="mt-4 text-center text-xs text-gray-400">Demo: admin@faststock.test / password &middot; staff@faststock.test / password</p>
</main>
</body>
</html>
