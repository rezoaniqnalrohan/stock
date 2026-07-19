<x-layouts.app title="Add Customer">
    <form method="POST" action="{{ route('customers.store') }}" class="max-w-2xl">@csrf
        @include('customers._form', ['submit' => 'Add Customer'])
    </form>
</x-layouts.app>
