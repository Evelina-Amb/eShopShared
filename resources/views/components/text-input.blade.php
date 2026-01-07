@props(['disabled' => false])

<input
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge([
        'class' =>
            'border border-gray-300 bg-white text-gray-900
             focus:border-blue-500 focus:ring focus:ring-blue-500/20
             rounded-md shadow-sm
             disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed'
    ]) !!}
>
