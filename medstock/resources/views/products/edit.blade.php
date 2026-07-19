<x-layouts.app title="Edit Product" subtitle="{{ $product->name }}">
    <form method="POST" action="{{ route('products.update', $product) }}" class="max-w-3xl">
        @csrf @method('PUT')
        @include('products._form', ['submit' => 'Save Changes'])
    </form>
</x-layouts.app>
