@props([
    'align' => 'left',
    'width' => '48',
    'contentClasses' => 'py-1 bg-white ring-1 ring-black ring-opacity-5',
    'triggerClasses' => 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out',
    'active' => false
])

@php
$alignmentClasses = match ($align) {
    'left' => 'origin-top-left left-0',
    'right' => 'origin-top-right right-0',
    default => 'origin-top-left left-0',
};

$widthClass = match ($width) {
    '48' => 'w-48',
    '56' => 'w-56',
    '60' => 'w-60', // Added a bit more width if needed
    'auto' => 'w-auto',
    default => 'w-48',
};

$activeClasses = $active
                ? 'border-indigo-500 text-gray-900'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        <button type="button" class="{{ $triggerClasses }} {{ $activeClasses }}" :aria-expanded="open.toString()">
            {{ $trigger }}
            <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 mt-2 {{ $widthClass }} rounded-md shadow-lg {{ $alignmentClasses }}"
         style="display: none;"
         @click="open = false">
        <div class="rounded-md {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
