@extends('layouts.app')
@section('title', 'Setup')

@section('content')
<x-page-header title="Setup" subtitle="Outlets, categories, brands and users" />

<div class="grid lg:grid-cols-2 gap-5">
    {{-- Outlets --}}
    <x-card>
        <h3 class="font-display text-lg font-bold mb-3">Outlets</h3>
        <div class="space-y-2 mb-4">
            @foreach ($outlets as $o)
                <div class="flex items-center justify-between text-sm border-b border-line pb-2">
                    <div>
                        <span class="font-medium">{{ $o->name }}</span>
                        <span class="text-xs text-muted">· {{ $o->code }} · {{ $o->location }}</span>
                        @if ($o->is_warehouse)<span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-cream text-muted ml-1">Warehouse</span>@endif
                    </div>
                    <span class="text-xs text-muted">{{ $o->stocks_count }} SKUs</span>
                </div>
            @endforeach
        </div>
        <form method="POST" action="{{ route('setup.outlets.store') }}" class="grid grid-cols-2 gap-2">
            @csrf
            <input name="name" required placeholder="Outlet name" class="col-span-2 rounded-xl border border-line px-3 py-2 text-sm">
            <input name="code" required placeholder="Code" class="rounded-xl border border-line px-3 py-2 text-sm">
            <input name="location" placeholder="Location" class="rounded-xl border border-line px-3 py-2 text-sm">
            <label class="col-span-2 flex items-center gap-2 text-sm text-muted">
                <input type="checkbox" name="is_warehouse" value="1" class="rounded border-line text-accent focus:ring-accent"> Is a warehouse
            </label>
            <button class="col-span-2 rounded-xl bg-sidebar text-white text-sm font-semibold py-2 hover:bg-black transition">Add outlet</button>
        </form>
    </x-card>

    {{-- Users --}}
    <x-card>
        <h3 class="font-display text-lg font-bold mb-3">Users & Roles</h3>
        <div class="space-y-2 mb-4">
            @foreach ($users as $u)
                <div class="flex items-center justify-between text-sm border-b border-line pb-2">
                    <div>
                        <span class="font-medium">{{ $u->name }}</span>
                        <span class="text-xs text-muted">· {{ $u->email }}</span>
                    </div>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-lg bg-accent/15 text-accentdk capitalize">{{ $u->role }}</span>
                </div>
            @endforeach
        </div>
        <form method="POST" action="{{ route('setup.users.store') }}" class="grid grid-cols-2 gap-2">
            @csrf
            <input name="name" required placeholder="Name" class="rounded-xl border border-line px-3 py-2 text-sm">
            <input name="email" type="email" required placeholder="Email" class="rounded-xl border border-line px-3 py-2 text-sm">
            <select name="role" class="rounded-xl border border-line px-3 py-2 text-sm">
                <option value="cashier">Cashier</option>
                <option value="manager">Manager</option>
                <option value="admin">Admin</option>
            </select>
            <select name="outlet_id" class="rounded-xl border border-line px-3 py-2 text-sm">
                <option value="">No outlet</option>
                @foreach ($outlets as $o)<option value="{{ $o->id }}">{{ $o->name }}</option>@endforeach
            </select>
            <input name="password" type="password" required placeholder="Password" class="col-span-2 rounded-xl border border-line px-3 py-2 text-sm">
            <button class="col-span-2 rounded-xl bg-sidebar text-white text-sm font-semibold py-2 hover:bg-black transition">Add user</button>
        </form>
    </x-card>

    {{-- Categories --}}
    <x-card>
        <h3 class="font-display text-lg font-bold mb-3">Categories</h3>
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach ($categories as $c)
                <span class="text-sm bg-cream rounded-lg px-3 py-1.5">{{ $c->name }} <span class="text-muted text-xs">{{ $c->products_count }}</span></span>
            @endforeach
        </div>
        <form method="POST" action="{{ route('setup.categories.store') }}" class="flex gap-2">
            @csrf
            <input name="name" required placeholder="New category" class="flex-1 rounded-xl border border-line px-3 py-2 text-sm">
            <button class="rounded-xl bg-accent text-sidebar text-sm font-semibold px-4 py-2">Add</button>
        </form>
    </x-card>

    {{-- Brands --}}
    <x-card>
        <h3 class="font-display text-lg font-bold mb-3">Brands</h3>
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach ($brands as $b)
                <span class="text-sm bg-cream rounded-lg px-3 py-1.5">{{ $b->name }} <span class="text-muted text-xs">{{ $b->products_count }}</span></span>
            @endforeach
        </div>
        <form method="POST" action="{{ route('setup.brands.store') }}" class="flex gap-2">
            @csrf
            <input name="name" required placeholder="New brand" class="flex-1 rounded-xl border border-line px-3 py-2 text-sm">
            <button class="rounded-xl bg-accent text-sidebar text-sm font-semibold px-4 py-2">Add</button>
        </form>
    </x-card>
</div>
@endsection
