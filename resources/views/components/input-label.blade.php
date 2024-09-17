@props(['value'])
@props(['required' => false])

<label {{ $attributes->merge(['class' => 'block font-medium text-md text-gray-300']) }}>
    {{ $value ?? $slot }}

    @if ($required)
        <abbr class="req" title="{{ __("Pflichtfeld") }}">*</abbr>
    @endif
</label>
