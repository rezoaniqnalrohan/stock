@props(['title' => null, 'subtitle' => null, 'padding' => 'p-5'])
<div {{ $attributes->merge(['class' => 'rounded-2xl bg-white ring-1 ring-slate-200/70 shadow-sm']) }}>
    @if ($title || isset($actions))
        <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
            <div>
                @if ($title)<h2 class="font-semibold text-slate-900">{{ $title }}</h2>@endif
                @if ($subtitle)<p class="text-xs text-slate-400">{{ $subtitle }}</p>@endif
            </div>
            @isset($actions)<div>{{ $actions }}</div>@endisset
        </div>
    @endif
    <div class="{{ $padding ?? 'p-5' }}">{{ $slot }}</div>
</div>
