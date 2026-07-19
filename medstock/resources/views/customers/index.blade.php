<x-layouts.app title="Customers" subtitle="{{ $customers->total() }} clinics, pharmacies &amp; hospitals">
    <x-slot:action>
        <a href="{{ route('customers.create') }}" class="inline-flex items-center gap-2 rounded-full bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 shadow-sm">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg> Add Customer
        </a>
    </x-slot:action>

    <x-card padding="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs uppercase tracking-wider text-slate-400 border-b border-slate-100">
                    <th class="px-5 py-3 font-medium">Customer</th><th class="px-5 py-3 font-medium">Type</th>
                    <th class="px-5 py-3 font-medium">Contact</th><th class="px-5 py-3 font-medium">Orders</th>
                    <th class="px-5 py-3 font-medium text-right">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @php $typeColors = ['clinic'=>'bg-sky-50 text-sky-700','pharmacy'=>'bg-violet-50 text-violet-700','hospital'=>'bg-teal-50 text-teal-700']; @endphp
                    @forelse ($customers as $c)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3"><a href="{{ route('customers.show', $c) }}" class="font-semibold text-slate-800 hover:text-teal-600">{{ $c->name }}</a></td>
                            <td class="px-5 py-3"><span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $typeColors[$c->type] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($c->type) }}</span></td>
                            <td class="px-5 py-3 text-slate-500">{{ $c->email ?? '—' }}<br><span class="text-xs text-slate-400">{{ $c->phone }}</span></td>
                            <td class="px-5 py-3 tabular-nums font-medium">{{ $c->sales_orders_count }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('customers.edit', $c) }}" class="text-slate-400 hover:text-teal-600" title="Edit">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('customers.destroy', $c) }}" onsubmit="return confirm('Delete customer?')">@csrf @method('DELETE')
                                        <button class="text-slate-400 hover:text-rose-500" title="Delete">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">No customers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $customers->links() }}</div>
    </x-card>
</x-layouts.app>
