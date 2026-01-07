@props(['disabled' => false])

<button
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge([
        'class' =>
            'inline-flex items-center px-4 py-2
             bg-blue-600 hover:bg-blue-500
             text-white font-semibold
             rounded-md
             transition
             disabled:opacity-50 disabled:cursor-not-allowed'
    ]) !!}
>
    {{ $slot }}
</button>
