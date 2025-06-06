@props(['title' => null])

<div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    @if ($title)
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ $title }}</h3>
    @endif

    {{ $slot }}
</div>
