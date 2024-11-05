@props(['disabled' => false, 'name', 'svgName', 'isChecked' => false])
<input id="{{ $name }}" name="{{ $name }}" type="checkbox" {{ $disabled ? 'disabled' : '' }} class="hidden_checkbox image_toggle" {{ $isChecked ? 'checked' : '' }}>
<label for="{{ $name }}" {!! $attributes->merge(['class' => 'image_toggle_label']) !!}>
    <img class="text-white" src="{{ route('image.show', $svgName . ".svg") }}" alt="">
    <span>{{ $slot }}</span>
</label>
