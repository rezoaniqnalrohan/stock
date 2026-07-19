@extends('layouts.app')
@section('title', 'Sell')

@section('content')
<x-page-header title="Sell" subtitle="Point of sale — ring up a customer">
    <x-slot:actions>
        <form method="GET" action="{{ route('pos') }}">
            <select name="outlet" onchange="this.form.submit()" class="rounded-xl border border-line bg-white px-3.5 py-2 text-sm font-medium">
                @foreach ($outlets as $o)
                    <option value="{{ $o->id }}" @selected($outletId === $o->id)>{{ $o->name }}</option>
                @endforeach
            </select>
        </form>
    </x-slot:actions>
</x-page-header>

<div class="grid lg:grid-cols-3 gap-5">
    {{-- Product grid --}}
    <div class="lg:col-span-2">
        <x-card pad="p-3" class="mb-4">
            <input id="posSearch" placeholder="Search products…" oninput="filterProducts(this.value)"
                   class="w-full rounded-xl border border-line px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-accent/40">
        </x-card>
        <div id="productGrid" class="grid sm:grid-cols-2 xl:grid-cols-3 gap-3">
            @foreach ($products as $p)
                <button type="button" data-name="{{ strtolower($p->name) }}"
                        onclick="addToCart({{ $p->id }}, {{ json_encode($p->name) }}, {{ $p->price }}, {{ $p->qty }})"
                        class="text-left bg-card rounded-2xl border border-line p-3 hover:border-accent hover:shadow-sm transition {{ $p->qty <= 0 ? 'opacity-50' : '' }}">
                    <div class="flex items-start gap-2.5">
                        <div class="w-11 h-11 rounded-xl bg-cream grid place-items-center text-xl shrink-0">{{ $p->image_url ?? '📦' }}</div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold leading-tight truncate">{{ $p->name }}</p>
                            <p class="text-xs text-muted truncate">{{ $p->variant }}</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-2.5">
                        <span class="font-display font-bold">${{ number_format($p->price, 2) }}</span>
                        <span class="text-xs {{ $p->qty <= 0 ? 'text-red-600' : 'text-muted' }}">{{ $p->qty }} in stock</span>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Cart --}}
    <div>
        <x-card class="sticky top-6">
            <h3 class="font-display text-lg font-bold mb-1">Current sale</h3>
            <p class="text-sm text-muted mb-4">{{ $outlets->firstWhere('id', $outletId)?->name }}</p>

            <form method="POST" action="{{ route('pos.store') }}" id="saleForm">
                @csrf
                <input type="hidden" name="outlet_id" value="{{ $outletId }}">
                <select name="customer_id" class="w-full rounded-xl border border-line px-3 py-2.5 text-sm mb-3">
                    <option value="">Walk-in customer</option>
                    @foreach ($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>

                <div id="cartLines" class="space-y-2 min-h-[80px]"></div>
                <div id="emptyCart" class="text-sm text-muted text-center py-6">Tap a product to add it.</div>

                <div class="border-t border-line mt-4 pt-4 flex items-center justify-between">
                    <span class="text-sm text-muted">Total</span>
                    <span id="cartTotal" class="font-display text-2xl font-bold">$0.00</span>
                </div>
                <button type="submit" id="checkoutBtn" disabled
                        class="w-full mt-4 rounded-xl bg-sidebar text-white text-sm font-semibold py-3 hover:bg-black transition disabled:opacity-40 disabled:cursor-not-allowed">
                    Complete sale
                </button>
            </form>
        </x-card>
    </div>
</div>

@push('scripts')
<script>
    const cart = {};

    function addToCart(id, name, price, stock) {
        if (stock <= 0) return;
        if (!cart[id]) cart[id] = { id, name, price, qty: 0 };
        if (cart[id].qty < stock) cart[id].qty++;
        render();
    }
    function setQty(id, delta) {
        if (!cart[id]) return;
        cart[id].qty += delta;
        if (cart[id].qty <= 0) delete cart[id];
        render();
    }
    function render() {
        const lines = document.getElementById('cartLines');
        const keys = Object.keys(cart);
        document.getElementById('emptyCart').style.display = keys.length ? 'none' : 'block';
        let total = 0;
        lines.innerHTML = keys.map(k => {
            const i = cart[k];
            total += i.price * i.qty;
            return `<div class="flex items-center gap-2">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate">${i.name}</p>
                    <p class="text-xs text-muted">$${i.price.toFixed(2)} each</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="setQty(${i.id},-1)" class="w-6 h-6 rounded-lg border border-line text-muted">−</button>
                    <span class="w-6 text-center text-sm font-semibold tabular-nums">${i.qty}</span>
                    <button type="button" onclick="setQty(${i.id},1)" class="w-6 h-6 rounded-lg border border-line text-muted">+</button>
                </div>
                <span class="w-16 text-right text-sm font-semibold tabular-nums">$${(i.price*i.qty).toFixed(2)}</span>
                <input type="hidden" name="items[${i.id}][product_id]" value="${i.id}">
                <input type="hidden" name="items[${i.id}][quantity]" value="${i.qty}">
            </div>`;
        }).join('');
        document.getElementById('cartTotal').textContent = '$' + total.toFixed(2);
        document.getElementById('checkoutBtn').disabled = keys.length === 0;
    }
    function filterProducts(q) {
        q = q.toLowerCase();
        document.querySelectorAll('#productGrid [data-name]').forEach(el => {
            el.style.display = el.dataset.name.includes(q) ? '' : 'none';
        });
    }
</script>
@endpush
@endsection
