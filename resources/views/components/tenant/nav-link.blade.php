@props(['active' => false])

@php
$classes = $active
    ? 'flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg'
    : 'flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
