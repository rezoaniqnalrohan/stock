<x-layouts.app title="Add Supplier">
    <form method="POST" action="{{ route('suppliers.store') }}" class="max-w-2xl">@csrf
        @include('suppliers._form', ['submit' => 'Add Supplier'])
    </form>
</x-layouts.app>
