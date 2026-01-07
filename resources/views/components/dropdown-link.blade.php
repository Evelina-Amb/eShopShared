@props(['active'])

@php
$classes = ($active ?? false)
    ? 'block w-full px-4 py-2 text-start text-sm leading-5 text-black bg-gray-100'
    : 'block w-full px-4 py-2 text-start text-sm leading-5 text-black hover:bg-gray-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
