@extends('layouts.app')
@section('title', 'New sale')
@section('content')
<div class="grid gap-5 lg:grid-cols-[1fr_320px]">
    <div class="grid content-start gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($items as $item)
            @php $available = $item->ingredients->isEmpty() ? null : $item->available(); @endphp
            <button type="button"
                    @if ($available === 0) disabled @endif
                    data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->price }}" data-available="{{ $available ?? '' }}"
                    class="pos-item cursor-pointer rounded-3xl bg-white p-5 text-left shadow-sm transition hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand active:scale-[.98] disabled:cursor-not-allowed disabled:opacity-40">
                <p class="font-semibold">{{ $item->name }}</p>
                <p class="num mt-1 text-lg font-bold text-brand">৳{{ number_format($item->price) }}</p>
                <p class="mt-1 text-xs text-gray-400">
                    @if (is_null($available)) no recipe — stock not tracked
                    @elseif ($available === 0) out of stock
                    @else can make {{ $available }}
                    @endif
                </p>
            </button>
        @empty
            <p class="col-span-full rounded-3xl bg-white p-10 text-center text-gray-400 shadow-sm">No menu items yet — add them under Menu items.</p>
        @endforelse
    </div>

    <div class="lg:sticky lg:top-6 h-fit rounded-3xl bg-white p-6 shadow-sm">
        <h2 class="mb-4 font-semibold">Order</h2>
        <form method="POST" action="{{ route('sales.store') }}" id="pos-form">
            @csrf
            <ul id="cart" class="mb-4 space-y-2 text-sm"></ul>
            <p id="cart-empty" class="mb-4 rounded-xl bg-canvas px-4 py-6 text-center text-sm text-gray-400">Tap items to add them.</p>
            <div class="mb-4 flex items-center justify-between border-t border-gray-100 pt-4">
                <span class="text-sm font-medium text-gray-500">Total</span>
                <span id="total" class="num text-2xl font-bold">৳0</span>
            </div>
            <button id="submit-btn" disabled
                    class="w-full cursor-pointer rounded-full bg-brand py-3 text-sm font-semibold text-white shadow-lg shadow-brand/30 transition hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-40">
                Record sale
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const cart = {}; // id -> {name, price, qty, available}

    function render() {
        const ul = document.getElementById('cart');
        ul.innerHTML = '';
        let total = 0, count = 0;
        for (const [id, row] of Object.entries(cart)) {
            total += row.price * row.qty;
            count += row.qty;
            const li = document.createElement('li');
            li.className = 'flex items-center gap-2';
            li.innerHTML = `
                <span class="min-w-0 flex-1 truncate font-medium">${row.name}</span>
                <button type="button" aria-label="One less ${row.name}" class="grid h-7 w-7 cursor-pointer place-items-center rounded-lg bg-canvas font-bold hover:bg-gray-200">−</button>
                <span class="num w-6 text-center font-semibold">${row.qty}</span>
                <button type="button" aria-label="One more ${row.name}" class="grid h-7 w-7 cursor-pointer place-items-center rounded-lg bg-canvas font-bold hover:bg-gray-200">+</button>
                <span class="num w-16 text-right text-gray-500">৳${(row.price * row.qty).toLocaleString()}</span>
                <input type="hidden" name="items[${id}]" value="${row.qty}">`;
            const [minus, plus] = li.querySelectorAll('button');
            minus.onclick = () => { if (--row.qty <= 0) delete cart[id]; render(); };
            plus.onclick = () => { add(id, row); };
            ul.appendChild(li);
        }
        document.getElementById('cart-empty').style.display = count ? 'none' : '';
        document.getElementById('total').textContent = '৳' + total.toLocaleString();
        document.getElementById('submit-btn').disabled = !count;
    }

    function add(id, row) {
        if (row.available !== null && row.qty >= row.available) return; // can't sell more than stock can make
        row.qty++;
        render();
    }

    document.querySelectorAll('.pos-item').forEach(btn => btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        cart[id] ??= { name: btn.dataset.name, price: +btn.dataset.price, qty: 0,
                       available: btn.dataset.available === '' ? null : +btn.dataset.available };
        add(id, cart[id]);
    }));

    document.getElementById('pos-form').addEventListener('submit', () => {
        document.getElementById('submit-btn').disabled = true; // no double-submits
    });
</script>
@endpush
