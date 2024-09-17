@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'light_bg transition focus:transition border-2 border-gray-500 blue_border_focus rounded-md shadow-sm']) !!}>
