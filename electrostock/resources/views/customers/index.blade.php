@extends('layouts.app')
@section('title', 'Customers')

@section('content')
<x-page-header title="Customers" subtitle="Your retail CRM" />

<div class="grid lg:grid-cols-3 gap-5">
    <x-card class="lg:col-span-2 overflow-hidden" pad="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-muted border-b border-line">
                        <th class="font-medium px-5 py-3">Customer</th>
                        <th class="font-medium px-5 py-3">Phone</th>
                        <th class="font-medium px-5 py-3 text-right">Orders</th>
                        <th class="font-medium px-5 py-3 text-right">Total spent</th>
                        <th class="font-medium px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($customers as $c)
                        <tr class="hover:bg-cream/60">
                            <td class="px-5 py-3">
                                <p class="font-medium">{{ $c->name }}</p>
                                <p class="text-xs text-muted">{{ $c->email }}</p>
                            </td>
                            <td class="px-5 py-3 text-muted">{{ $c->phone }}</td>
                            <td class="px-5 py-3 text-right tabular-nums">{{ $c->sales_count }}</td>
                            <td class="px-5 py-3 text-right font-semibold tabular-nums">${{ number_format($c->total_spent, 2) }}</td>
                            <td class="px-5 py-3 text-right">
                                <form method="POST" action="{{ route('customers.destroy', $c) }}" onsubmit="return confirm('Remove customer?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 text-sm font-medium hover:underline">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-10">No customers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <x-card>
        <h3 class="font-display text-lg font-bold mb-3">Add customer</h3>
        <form method="POST" action="{{ route('customers.store') }}" class="space-y-3">
            @csrf
            <input name="name" required placeholder="Full name" class="w-full rounded-xl border border-line px-3.5 py-2.5 text-sm">
            <input name="email" type="email" placeholder="Email" class="w-full rounded-xl border border-line px-3.5 py-2.5 text-sm">
            <input name="phone" placeholder="Phone" class="w-full rounded-xl border border-line px-3.5 py-2.5 text-sm">
            <button class="w-full rounded-xl bg-sidebar text-white text-sm font-semibold py-2.5 hover:bg-black transition">Add customer</button>
        </form>
    </x-card>
</div>
@endsection
