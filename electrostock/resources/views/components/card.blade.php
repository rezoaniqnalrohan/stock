@props(['pad' => 'p-5'])
<div {{ $attributes->merge(['class' => "bg-card rounded-2xl border border-line $pad"]) }}>
    {{ $slot }}
</div>
