@extends('layouts.app')
@section('title', 'Products')
@section('heading', 'Products & Catalog')
@section('subheading', 'Manage SKUs, pricing and storage requirements')

@section('actions')
    <a href="/products/create" class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium shadow-sm shadow-brand-200 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
        New product
    </a>
@endsection

@section('content')
<x-card>
    <form method="GET" class="flex flex-wrap items-center gap-3 mb-4">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M21 21l-4.3-4.3M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
            <input name="search" value="{{ request('search') }}" placeholder="Search name or SKU…"
                   class="w-full h-10 pl-9 pr-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none text-sm">
        </div>
        <select name="category" class="h-10 px-3 rounded-xl border border-slate-200 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none">
            <option value="">All categories</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <button class="h-10 px-4 rounded-xl bg-slate-800 text-white text-sm font-medium">Filter</button>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100">
                    <th class="py-3 px-2 font-semibold">SKU</th>
                    <th class="py-3 px-2 font-semibold">Product</th>
                    <th class="py-3 px-2 font-semibold">Category</th>
                    <th class="py-3 px-2 font-semibold">Storage</th>
                    <th class="py-3 px-2 font-semibold text-right">On hand</th>
                    <th class="py-3 px-2 font-semibold text-right">Price</th>
                    <th class="py-3 px-2 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($products as $p)
                    @php $temp = ['ambient' => 'slate', 'chilled' => 'blue', 'frozen' => 'violet'][$p->storage_temp]; @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-2 font-mono text-xs text-slate-500">{{ $p->sku }}</td>
                        <td class="py-3 px-2 font-medium text-slate-800">
                            <a href="/products/{{ $p->id }}" class="hover:text-brand-600">{{ $p->name }}</a>
                            <span class="block text-xs text-slate-400">{{ $p->unit->abbreviation }} · {{ $p->supplier?->name ?? '—' }}</span>
                        </td>
                        <td class="py-3 px-2">
                            <span class="inline-flex items-center gap-1.5 text-slate-600">
                                <span class="w-2.5 h-2.5 rounded-full" style="background: {{ $p->category->color }}"></span>
                                {{ $p->category->name }}
                            </span>
                        </td>
                        <td class="py-3 px-2"><x-badge :color="$temp">{{ ucfirst($p->storage_temp) }}</x-badge></td>
                        <td class="py-3 px-2 text-right tabular-nums font-semibold {{ $p->isLowStock() ? 'text-rose-600' : 'text-slate-800' }}">
                            {{ number_format($p->stockOnHand()) }}
                            @if ($p->isLowStock())<span class="block text-[10px] font-normal text-rose-500">low · rop {{ $p->reorder_point }}</span>@endif
                        </td>
                        <td class="py-3 px-2 text-right tabular-nums text-slate-700">${{ number_format($p->price, 2) }}</td>
                        <td class="py-3 px-2 text-right whitespace-nowrap">
                            <a href="/products/{{ $p->id }}/edit" class="text-slate-400 hover:text-brand-600 p-1 inline-block" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.4-9.4a2 2 0 112.8 2.8L11.8 15 8 16l1-3.8 8.6-8.6z"/></svg>
                            </a>
                            <form action="/products/{{ $p->id }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
                                @csrf @method('DELETE')
                                <button class="text-slate-400 hover:text-rose-600 p-1" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.9 12a2 2 0 01-2 1.9H7.9a2 2 0 01-2-1.9L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><x-empty message="No products match your search." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
</x-card>
@endsection
