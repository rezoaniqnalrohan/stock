<x-layouts.app title="Add Product" subtitle="Create a new catalog item">
    <form method="POST" action="{{ route('products.store') }}" class="max-w-3xl">
        @csrf
        @include('products._form', ['submit' => 'Create Product'])
    </form>
</x-layouts.app>
