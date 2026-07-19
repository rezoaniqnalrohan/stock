@props(['title' => null, 'subtitle' => null, 'action' => null])
<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-sm p-5']) }}>
    @if ($title || $action)
        <div class="flex items-start justify-between mb-4">
            <div>
                @if ($title)<h3 class="font-bold text-slate-900">{{ $title }}</h3>@endif
                @if ($subtitle)<p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>@endif
            </div>
            @if ($action){{ $action }}@endif
        </div>
    @endif
    {{ $slot }}
</div>
