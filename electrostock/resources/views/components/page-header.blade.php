@props(['title', 'subtitle' => null])
<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-ink">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-muted text-sm mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
