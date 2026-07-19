<x-layouts.app title="Suppliers" subtitle="{{ $suppliers->total() }} manufacturers &amp; distributors">
    <x-slot:action>
        <a href="{{ route('suppliers.create') }}" class="inline-flex items-center gap-2 rounded-full bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 shadow-sm">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg> Add Supplier
        </a>
    </x-slot:action>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse ($suppliers as $s)
            <x-card>
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-11 w-11 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold">{{ strtoupper(substr($s->name,0,2)) }}</div>
                        <div>
                            <a href="{{ route('suppliers.show', $s) }}" class="font-semibold text-slate-800 hover:text-teal-600">{{ $s->name }}</a>
                            <p class="text-xs text-slate-400">{{ $s->contact_name ?? 'No contact' }}</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ $s->products_count }} SKUs</span>
                </div>
                <div class="mt-4 space-y-1.5 text-sm text-slate-500">
                    <p class="flex items-center gap-2"><svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M3 8l9 6 9-6M3 8v10h18V8M3 8l9-6 9 6"/></svg>{{ $s->email ?? '—' }}</p>
                    <p class="flex items-center gap-2"><svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M3 5a2 2 0 012-2h3l2 5-3 2a11 11 0 005 5l2-3 5 2v3a2 2 0 01-2 2A16 16 0 013 5z"/></svg>{{ $s->phone ?? '—' }}</p>
                </div>
                <div class="mt-4 flex gap-2 border-t border-slate-100 pt-3">
                    <a href="{{ route('suppliers.edit', $s) }}" class="flex-1 text-center rounded-lg border border-slate-300 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-50">Edit</a>
                    <form method="POST" action="{{ route('suppliers.destroy', $s) }}" class="flex-1" onsubmit="return confirm('Delete supplier?')">@csrf @method('DELETE')
                        <button class="w-full rounded-lg border border-rose-200 py-1.5 text-sm font-medium text-rose-600 hover:bg-rose-50">Delete</button>
                    </form>
                </div>
            </x-card>
        @empty
            <p class="text-slate-400">No suppliers yet.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $suppliers->links() }}</div>
</x-layouts.app>
