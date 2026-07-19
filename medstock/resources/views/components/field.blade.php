@props(['label', 'name', 'type' => 'text', 'value' => null, 'required' => false, 'options' => null, 'selected' => null, 'placeholder' => null, 'step' => null])
@php $val = old($name, $value); @endphp
<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">{{ $label }} @if($required)<span class="text-rose-500">*</span>@endif</label>
    @php $base = 'w-full rounded-lg border px-3 py-2.5 text-sm outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 '.($errors->has($name) ? 'border-rose-400' : 'border-slate-300'); @endphp
    @if ($type === 'select')
        <select name="{{ $name }}" @if($required) required @endif class="{{ $base }} bg-white">
            @if ($placeholder)<option value="">{{ $placeholder }}</option>@endif
            @foreach ($options as $optVal => $optLabel)
                <option value="{{ $optVal }}" @selected((string) old($name, $selected) === (string) $optVal)>{{ $optLabel }}</option>
            @endforeach
        </select>
    @elseif ($type === 'textarea')
        <textarea name="{{ $name }}" rows="3" placeholder="{{ $placeholder }}" class="{{ $base }}">{{ $val }}</textarea>
    @else
        <input type="{{ $type }}" name="{{ $name }}" value="{{ $val }}" @if($required) required @endif
            @if($step) step="{{ $step }}" @endif placeholder="{{ $placeholder }}" class="{{ $base }}">
    @endif
    @error($name)<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
</div>
