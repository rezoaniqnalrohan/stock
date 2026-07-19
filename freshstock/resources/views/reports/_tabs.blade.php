@php
    $tabs = [
        'Stock valuation' => '/reports/valuation',
        'Movement history' => '/reports/movement',
        'Expiring stock' => '/reports/expiring',
        'Wastage' => '/reports/wastage',
    ];
    $current = '/'.request()->path();
@endphp
<div class="flex flex-wrap gap-2 mb-4">
    @foreach ($tabs as $label => $url)
        <a href="{{ $url }}"
           class="px-4 h-10 inline-flex items-center rounded-xl text-sm font-medium transition
                  {{ $current === $url ? 'bg-brand-600 text-white shadow-sm shadow-brand-200' : 'bg-white text-slate-600 hover:bg-slate-50 shadow-sm' }}">
            {{ $label }}
        </a>
    @endforeach
</div>
