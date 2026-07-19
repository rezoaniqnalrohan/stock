@extends('layouts.app')
@section('title', 'Team')
@section('content')
<div class="grid gap-5 lg:grid-cols-[320px_1fr]">
    <div class="h-fit rounded-3xl bg-white p-6 shadow-sm">
        <h2 class="mb-4 font-semibold">Add team member</h2>
        <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium">Name <span class="text-brand">*</span></label>
                <input id="name" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
            </div>
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium">Email <span class="text-brand">*</span></label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                       class="w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
            </div>
            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium">Password <span class="text-brand">*</span></label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                       class="w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
                <p class="mt-1 text-xs text-gray-400">Minimum 8 characters.</p>
            </div>
            <div>
                <label for="role" class="mb-1.5 block text-sm font-medium">Role <span class="text-brand">*</span></label>
                <select id="role" name="role" required class="w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
                    <option value="staff" @selected(old('role') === 'staff')>Staff — record stock &amp; sales</option>
                    <option value="admin" @selected(old('role') === 'admin')>Admin — full access</option>
                </select>
            </div>
            <button class="w-full cursor-pointer rounded-full bg-ink py-2.5 text-sm font-semibold text-white transition hover:bg-black">Add member</button>
        </form>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm">
        <h2 class="mb-4 font-semibold">Team</h2>
        <ul class="divide-y divide-gray-100">
            @foreach ($users as $user)
                <li class="flex items-center gap-3 py-3">
                    <span class="grid h-10 w-10 place-items-center rounded-full bg-ink text-sm font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    <div>
                        <p class="font-medium">{{ $user->name }} @if($user->is(auth()->user()))<span class="text-xs text-gray-400">(you)</span>@endif</p>
                        <p class="text-sm text-gray-400">{{ $user->email }}</p>
                    </div>
                    <span class="ms-auto rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase {{ $user->role === 'admin' ? 'bg-brand/10 text-brand' : 'bg-gray-100 text-gray-500' }}">{{ $user->role }}</span>
                    @unless ($user->is(auth()->user()))
                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Remove {{ $user->name }}?')">
                            @csrf @method('DELETE')
                            <button aria-label="Delete {{ $user->name }}"
                                    class="grid h-8 w-8 cursor-pointer place-items-center rounded-lg text-gray-400 transition hover:bg-brand/10 hover:text-brand"><x-icon name="trash" class="h-4 w-4"/></button>
                        </form>
                    @endunless
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
