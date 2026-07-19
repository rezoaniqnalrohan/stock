<x-layouts.app title="Edit Customer" subtitle="{{ $customer->name }}">
    <form method="POST" action="{{ route('customers.update', $customer) }}" class="max-w-2xl">@csrf @method('PUT')
        @include('customers._form', ['submit' => 'Save Changes'])
    </form>
</x-layouts.app>
