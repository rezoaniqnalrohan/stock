<x-layouts.app title="Edit Supplier" subtitle="{{ $supplier->name }}">
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="max-w-2xl">@csrf @method('PUT')
        @include('suppliers._form', ['submit' => 'Save Changes'])
    </form>
</x-layouts.app>
