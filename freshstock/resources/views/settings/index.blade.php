@extends('layouts.app')
@section('title', 'Settings')
@section('heading', 'Settings')
@section('subheading', 'Warehouses, categories, units and team access')

@section('content')
@php
    $field = 'w-full h-11 px-3.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none text-sm';
    $label = 'block text-sm font-medium text-slate-700 mb-1.5';
    $add = 'h-11 px-5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold shadow-sm shadow-brand-200';
    $row = 'flex items-center justify-between gap-3 py-2.5 border-b border-slate-100 last:border-0';
@endphp

<div class="grid xl:grid-cols-2 gap-4">
    {{-- Warehouses --}}
    <x-card title="Warehouses" subtitle="Storage sites, including cold-chain rooms">
        <div class="mb-4">
            @forelse ($warehouses as $w)
                <div class="{{ $row }}">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-slate-800 truncate">
                            {{ $w->name }}
                            @if ($w->is_cold_chain)
                                <span class="ml-1.5 align-middle text-[11px] font-semibold px-2 py-0.5 rounded-full bg-sky-100 text-sky-700">Cold chain</span>
                            @endif
                        </p>
                        <p class="text-xs text-slate-400">{{ $w->code }}@if ($w->location) · {{ $w->location }}@endif</p>
                    </div>
                    <form action="/settings/warehouse/{{ $w->id }}" method="POST" onsubmit="return confirm('Delete warehouse?')">
                        @csrf @method('DELETE')
                        <button class="text-slate-400 hover:text-rose-600 p-1" aria-label="Delete warehouse">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.9 12a2 2 0 01-2 1.9H7.9a2 2 0 01-2-1.9L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            @empty
                <x-empty message="No warehouses yet." />
            @endforelse
        </div>
        <form method="POST" action="/settings/warehouses" class="space-y-4 pt-4 border-t border-slate-100">
            @csrf
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="{{ $label }}">Name</label><input name="name" class="{{ $field }}" required></div>
                <div><label class="{{ $label }}">Code</label><input name="code" class="{{ $field }}" required></div>
            </div>
            <div><label class="{{ $label }}">Location</label><input name="location" class="{{ $field }}"></div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="is_cold_chain" value="1" class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-200">
                Cold-chain storage
            </label>
            <button class="{{ $add }}">Add warehouse</button>
        </form>
    </x-card>

    {{-- Categories --}}
    <x-card title="Categories" subtitle="Product groupings used across reports">
        <div class="mb-4">
            @forelse ($categories as $c)
                <div class="{{ $row }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-3 h-3 rounded-full shrink-0" style="background: {{ $c->color }}"></span>
                        <p class="text-sm font-medium text-slate-800 truncate">{{ $c->name }}</p>
                    </div>
                    <form action="/settings/category/{{ $c->id }}" method="POST" onsubmit="return confirm('Delete category?')">
                        @csrf @method('DELETE')
                        <button class="text-slate-400 hover:text-rose-600 p-1" aria-label="Delete category">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.9 12a2 2 0 01-2 1.9H7.9a2 2 0 01-2-1.9L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            @empty
                <x-empty message="No categories yet." />
            @endforelse
        </div>
        <form method="POST" action="/settings/categories" class="space-y-4 pt-4 border-t border-slate-100">
            @csrf
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="{{ $label }}">Name</label><input name="name" class="{{ $field }}" required></div>
                <div>
                    <label class="{{ $label }}">Colour</label>
                    <input name="color" type="color" value="#7c3aed" class="w-full h-11 px-1.5 rounded-xl border border-slate-200 outline-none" required>
                </div>
            </div>
            <button class="{{ $add }}">Add category</button>
        </form>
    </x-card>

    {{-- Units --}}
    <x-card title="Units" subtitle="Units of measure for stock quantities">
        <div class="mb-4">
            @forelse ($units as $u)
                <div class="{{ $row }}">
                    <p class="text-sm font-medium text-slate-800 truncate">
                        {{ $u->name }} <span class="text-slate-400 font-normal">({{ $u->abbreviation }})</span>
                    </p>
                    <form action="/settings/unit/{{ $u->id }}" method="POST" onsubmit="return confirm('Delete unit?')">
                        @csrf @method('DELETE')
                        <button class="text-slate-400 hover:text-rose-600 p-1" aria-label="Delete unit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.9 12a2 2 0 01-2 1.9H7.9a2 2 0 01-2-1.9L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            @empty
                <x-empty message="No units yet." />
            @endforelse
        </div>
        <form method="POST" action="/settings/units" class="space-y-4 pt-4 border-t border-slate-100">
            @csrf
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="{{ $label }}">Name</label><input name="name" class="{{ $field }}" required></div>
                <div><label class="{{ $label }}">Abbreviation</label><input name="abbreviation" maxlength="12" class="{{ $field }}" required></div>
            </div>
            <button class="{{ $add }}">Add unit</button>
        </form>
    </x-card>

    {{-- Users --}}
    <x-card title="Team" subtitle="Who can sign in and what they can do">
        <div class="mb-4">
            @foreach ($users as $u)
                <div class="{{ $row }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 grid place-items-center font-semibold text-sm shrink-0">
                            {{ strtoupper(substr($u->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $u->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ $u->email }} · {{ $u->roleLabel() }}</p>
                        </div>
                    </div>
                    @if ($u->id !== auth()->id())
                        <form action="/settings/user/{{ $u->id }}" method="POST" onsubmit="return confirm('Delete user?')">
                            @csrf @method('DELETE')
                            <button class="text-slate-400 hover:text-rose-600 p-1" aria-label="Delete user">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.9 12a2 2 0 01-2 1.9H7.9a2 2 0 01-2-1.9L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    @else
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">You</span>
                    @endif
                </div>
            @endforeach
        </div>
        <form method="POST" action="/settings/users" class="space-y-4 pt-4 border-t border-slate-100">
            @csrf
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="{{ $label }}">Name</label><input name="name" class="{{ $field }}" required></div>
                <div><label class="{{ $label }}">Email</label><input name="email" type="email" class="{{ $field }}" required></div>
                <div>
                    <label class="{{ $label }}">Role</label>
                    <select name="role" class="{{ $field }}" required>
                        <option value="admin">Admin</option>
                        <option value="warehouse_manager">Warehouse Manager</option>
                        <option value="procurement_officer">Procurement Officer</option>
                    </select>
                </div>
                <div><label class="{{ $label }}">Password</label><input name="password" type="password" minlength="6" class="{{ $field }}" required></div>
            </div>
            <button class="{{ $add }}">Add user</button>
        </form>
    </x-card>
</div>
@endsection
