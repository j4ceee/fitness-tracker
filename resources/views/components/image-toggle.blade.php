@props(['disabled' => false, 'name', 'svgName', 'isChecked' => false])
<input type="hidden" name="{{ $name }}" value="0">
<input id="{{ $name }}" name="{{ $name }}" type="checkbox" {{ $disabled ? 'disabled' : '' }} class="hidden_checkbox image_toggle" value="1" {{ $isChecked ? 'checked' : '' }}>
<label for="{{ $name }}" {!! $attributes->merge(['class' => 'image_toggle_label']) !!}>
    <img class="text-white" src="{{ route('image.show', $svgName . ".svg") }}" alt="">
    <span>{{ $slot }}</span>
</label>
