@props(['data' => [], 'color' => '#0d9488'])
@php
    $data = array_values($data);
    $max = max(1, max($data ?: [0]));
    $n = max(1, count($data));
    $w = 84; $h = 28; $gap = 2;
    $bw = ($w - ($n - 1) * $gap) / $n;
@endphp
<svg viewBox="0 0 {{ $w }} {{ $h }}" width="{{ $w }}" height="{{ $h }}" class="overflow-visible">
    @foreach ($data as $i => $v)
        @php $bh = max(2, round(($v / $max) * $h)); @endphp
        <rect x="{{ round($i * ($bw + $gap), 2) }}" y="{{ $h - $bh }}" width="{{ round($bw, 2) }}" height="{{ $bh }}" rx="1.2" fill="{{ $color }}" opacity="{{ 0.35 + 0.65 * ($v / $max) }}"/>
    @endforeach
</svg>
