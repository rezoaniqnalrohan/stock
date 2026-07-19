<x-layouts.app title="Settings" subtitle="Warehouses, categories, units and users">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Warehouses --}}
        <x-card title="Warehouses" subtitle="{{ $warehouses->count() }} locations">
            <div class="space-y-2 mb-5">
                @forelse ($warehouses as $w)
                    <div class="flex items-center justify-between rounded-lg border border-slate-100 px-3.5 py-2.5">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/></svg>
                            </div>
                            <div><p class="font-medium text-slate-800">{{ $w->name }}</p><p class="text-xs text-slate-400">{{ $w->location ?? '—' }}</p></div>
                        </div>
                        <form method="POST" action="{{ route('settings.destroy', ['warehouses', $w->id]) }}" onsubmit="return confirm('Remove warehouse?')">@csrf @method('DELETE')
                            <button class="text-slate-300 hover:text-rose-500"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No warehouses.</p>
                @endforelse
            </div>
            <form method="POST" action="{{ route('settings.store', 'warehouses') }}" class="flex gap-2 border-t border-slate-100 pt-4">@csrf
                <input name="name" placeholder="Warehouse name" required class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20">
                <input name="location" placeholder="Location" class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20">
                <button class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Add</button>
            </form>
        </x-card>

        {{-- Categories --}}
        <x-card title="Categories" subtitle="{{ $categories->count() }} product groups">
            <div class="flex flex-wrap gap-2 mb-5">
                @forelse ($categories as $c)
                    <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 pl-3 pr-1.5 py-1.5 text-sm font-medium text-slate-700">
                        {{ $c->name }}
                        <form method="POST" action="{{ route('settings.destroy', ['categories', $c->id]) }}" onsubmit="return confirm('Remove category?')">@csrf @method('DELETE')
                            <button class="text-slate-400 hover:text-rose-500"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </form>
                    </span>
                @empty
                    <p class="text-sm text-slate-400">No categories.</p>
                @endforelse
            </div>
            <form method="POST" action="{{ route('settings.store', 'categories') }}" class="flex gap-2 border-t border-slate-100 pt-4">@csrf
                <input name="name" placeholder="Category name" required class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20">
                <button class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Add</button>
            </form>
        </x-card>

        {{-- Units --}}
        <x-card title="Units of Measure" subtitle="{{ $units->count() }} units">
            <div class="space-y-2 mb-5">
                @forelse ($units as $u)
                    <div class="flex items-center justify-between rounded-lg border border-slate-100 px-3.5 py-2">
                        <p class="text-sm text-slate-700">{{ $u->name }} <span class="ml-1 rounded bg-slate-100 px-1.5 py-0.5 font-mono text-xs text-slate-500">{{ $u->abbreviation }}</span></p>
                        <form method="POST" action="{{ route('settings.destroy', ['units', $u->id]) }}" onsubmit="return confirm('Remove unit?')">@csrf @method('DELETE')
                            <button class="text-slate-300 hover:text-rose-500"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No units.</p>
                @endforelse
            </div>
            <form method="POST" action="{{ route('settings.store', 'units') }}" class="flex gap-2 border-t border-slate-100 pt-4">@csrf
                <input name="name" placeholder="Unit name (Box)" required class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20">
                <input name="abbreviation" placeholder="BX" required maxlength="12" class="w-20 rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20">
                <button class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Add</button>
            </form>
        </x-card>

        {{-- Users --}}
        <x-card title="Users &amp; Roles" subtitle="{{ $users->count() }} team members">
            <div class="space-y-2 mb-5">
                @php $roleColors = ['admin'=>'bg-teal-50 text-teal-700','manager'=>'bg-sky-50 text-sky-700','sales'=>'bg-violet-50 text-violet-700']; @endphp
                @foreach ($users as $u)
                    <div class="flex items-center justify-between rounded-lg border border-slate-100 px-3.5 py-2.5">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="h-8 w-8 rounded-full bg-slate-800 text-white flex items-center justify-center text-xs font-semibold">{{ strtoupper(substr($u->name,0,1)) }}</div>
                            <div class="min-w-0"><p class="font-medium text-slate-800 truncate">{{ $u->name }}</p><p class="text-xs text-slate-400 truncate">{{ $u->email }}</p></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $roleColors[$u->role] ?? 'bg-slate-100 text-slate-600' }}">{{ $u->roleLabel() }}</span>
                            @if ($u->id !== auth()->id())
                                <form method="POST" action="{{ route('settings.destroy', ['users', $u->id]) }}" onsubmit="return confirm('Remove user?')">@csrf @method('DELETE')
                                    <button class="text-slate-300 hover:text-rose-500"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <form method="POST" action="{{ route('settings.store', 'users') }}" class="grid grid-cols-2 gap-2 border-t border-slate-100 pt-4">@csrf
                <input name="name" placeholder="Full name" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20">
                <input name="email" type="email" placeholder="Email" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20">
                <select name="role" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white">
                    <option value="sales">Sales Rep</option>
                    <option value="manager">Warehouse Manager</option>
                    <option value="admin">Admin</option>
                </select>
                <input name="password" type="password" placeholder="Password (min 6)" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20">
                <button class="col-span-2 rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Add User</button>
            </form>
        </x-card>
    </div>
</x-layouts.app>
