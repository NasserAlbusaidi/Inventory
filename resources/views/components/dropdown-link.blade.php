@props(['href', 'active' => false])

@php
$classes = ($active ?? false)
            ? 'block w-full px-4 py-2 text-left text-sm leading-5 text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-900 focus:outline-none focus:bg-gray-200 dark:focus:bg-gray-800 transition duration-150 ease-in-out'
            : 'block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 transition duration-150 ease-in-out';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
