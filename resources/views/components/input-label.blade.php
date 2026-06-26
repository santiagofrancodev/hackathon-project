@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-body-text']) }}>
    {{ $value ?? $slot }}
</label>
