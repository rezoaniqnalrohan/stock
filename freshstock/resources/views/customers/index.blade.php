@extends('layouts.app')
@section('title', 'Customers')
@section('heading', 'Customers')
@section('subheading', 'Retailers and outlets you supply')

@section('actions')
    <a href="/customers/create" class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium shadow-sm shadow-brand-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
        New customer
    </a>
@endsection

@section('content')
<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse ($customers as $c)
        <x-card>
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-xl bg-sky-100 text-sky-700 grid place-items-center font-bold shrink-0">{{ strtoupper(substr($c->name, 0, 2)) }}</div>
                    <div class="min-w-0">
                        <p class="font-semibold text-slate-900 truncate">{{ $c->name }}</p>
                        <p class="text-xs text-slate-400">{{ $c->sales_orders_count }} orders</p>
                    </div>
                </div>
                <div class="flex gap-1 shrink-0">
                    <a href="/customers/{{ $c->id }}/edit" class="text-slate-400 hover:text-brand-600 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.4-9.4a2 2 0 112.8 2.8L11.8 15 8 16l1-3.8 8.6-8.6z"/></svg>
                    </a>
                    <form action="/customers/{{ $c->id }}" method="POST" onsubmit="return confirm('Delete customer?')">
                        @csrf @method('DELETE')
                        <button class="text-slate-400 hover:text-rose-600 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.9 12a2 2 0 01-2 1.9H7.9a2 2 0 01-2-1.9L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg></button>
                    </form>
                </div>
            </div>
            <dl class="mt-4 space-y-1.5 text-sm">
                <div class="flex items-center gap-2 text-slate-600"><span class="text-slate-400 w-16">Contact</span> {{ $c->contact_name ?? '—' }}</div>
                <div class="flex items-center gap-2 text-slate-600"><span class="text-slate-400 w-16">Email</span> {{ $c->email ?? '—' }}</div>
                <div class="flex items-center gap-2 text-slate-600"><span class="text-slate-400 w-16">Phone</span> {{ $c->phone ?? '—' }}</div>
                <div class="flex items-center gap-2 text-slate-600"><span class="text-slate-400 w-16">Location</span> {{ $c->address ?? '—' }}</div>
            </dl>
        </x-card>
    @empty
        <div class="sm:col-span-2 xl:col-span-3"><x-card><x-empty message="No customers yet." /></x-card></div>
    @endforelse
</div>
<div class="mt-4">{{ $customers->links() }}</div>
@endsection
