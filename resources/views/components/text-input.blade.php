@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-border-light focus:border-primary focus:ring-primary rounded-md shadow-sm']) }}>
