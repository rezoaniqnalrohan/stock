@extends('layouts.app')
@section('title', 'Suppliers')
@section('content')
<div class="grid gap-5 lg:grid-cols-[320px_1fr]">
    <div class="h-fit rounded-3xl bg-white p-6 shadow-sm">
        <h2 class="mb-4 font-semibold">Add supplier</h2>
        <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium">Name <span class="text-brand">*</span></label>
                <input id="name" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
            </div>
            <div>
                <label for="phone" class="mb-1.5 block text-sm font-medium">Phone</label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                       class="w-full rounded-xl border border-gray-200 bg-canvas px-3 py-2.5 text-sm outline-none focus:border-ink focus:bg-white">
            </div>
            <button class="w-full cursor-pointer rounded-full bg-ink py-2.5 text-sm font-semibold text-white transition hover:bg-black">Add supplier</button>
        </form>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm">
        <h2 class="mb-4 font-semibold">Suppliers</h2>
        <ul class="divide-y divide-gray-100">
            @forelse ($suppliers as $supplier)
                <li class="py-3">
                    <details>
                        <summary class="flex cursor-pointer list-none items-center gap-3">
                            <span class="grid h-9 w-9 place-items-center rounded-xl bg-canvas text-sm font-bold text-gray-500">{{ strtoupper(substr($supplier->name, 0, 1)) }}</span>
                            <span class="font-medium">{{ $supplier->name }}</span>
                            <span class="num text-sm text-gray-400">{{ $supplier->phone }}</span>
                            <span class="ms-auto text-xs font-medium text-gray-400">edit ▾</span>
                        </summary>
                        <div class="mt-3 flex flex-wrap items-end gap-2 rounded-xl bg-canvas p-3">
                            <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="flex flex-1 flex-wrap gap-2">
                                @csrf @method('PUT')
                                <label class="sr-only" for="s-name-{{ $supplier->id }}">Name</label>
                                <input id="s-name-{{ $supplier->id }}" name="name" value="{{ $supplier->name }}" required class="flex-1 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm outline-none focus:border-ink">
                                <label class="sr-only" for="s-phone-{{ $supplier->id }}">Phone</label>
                                <input id="s-phone-{{ $supplier->id }}" name="phone" type="tel" value="{{ $supplier->phone }}" class="w-36 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm outline-none focus:border-ink">
                                <button class="cursor-pointer rounded-full bg-ink px-4 py-2 text-xs font-semibold text-white hover:bg-black">Save</button>
                            </form>
                            <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Delete {{ $supplier->name }}?')">
                                @csrf @method('DELETE')
                                <button class="cursor-pointer rounded-full bg-brand/10 px-4 py-2 text-xs font-semibold text-brand hover:bg-brand/20">Delete</button>
                            </form>
                        </div>
                    </details>
                </li>
            @empty
                <li class="py-10 text-center text-gray-400">No suppliers yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
