@props([
    'title',
    'value',
    'icon',
    'color' => 'gray',
    'change' => null,
    'changeType' => 'neutral', // 'increase', 'decrease', 'neutral'
    'changeText' => '',
    'footerText' => ''
])

@php
    // These colors are now tuned to match your screenshot's light pastel style.
    // Dark mode uses a very subtle "glow" effect.
    $colorClasses = [
        'blue'    => ['bg' => 'bg-blue-100 dark:bg-blue-500/10', 'text' => 'text-blue-600 dark:text-blue-400'],
        'orange'  => ['bg' => 'bg-orange-100 dark:bg-orange-500/10', 'text' => 'text-orange-600 dark:text-orange-400'],
        'green'   => ['bg' => 'bg-green-100 dark:bg-green-500/10', 'text' => 'text-green-600 dark:text-green-400'],
        'purple'  => ['bg' => 'bg-purple-100 dark:bg-purple-500/10', 'text' => 'text-purple-600 dark:text-purple-400'],
        'indigo'  => ['bg' => 'bg-indigo-100 dark:bg-indigo-500/10', 'text' => 'text-indigo-600 dark:text-indigo-400'],
        'pink'    => ['bg' => 'bg-pink-100 dark:bg-pink-500/10', 'text' => 'text-pink-600 dark:text-pink-400'],
        'cyan'    => ['bg' => 'bg-cyan-100 dark:bg-cyan-500/10', 'text' => 'text-cyan-600 dark:text-cyan-400'],
        'teal'    => ['bg' => 'bg-teal-100 dark:bg-teal-500/10', 'text' => 'text-teal-600 dark:text-teal-400'],
        'yellow'  => ['bg' => 'bg-yellow-100 dark:bg-yellow-500/10', 'text' => 'text-yellow-600 dark:text-yellow-400'],
        'red'     => ['bg' => 'bg-red-100 dark:bg-red-500/10', 'text' => 'text-red-600 dark:text-red-400'],
        'sky'     => ['bg' => 'bg-sky-100 dark:bg-sky-500/10', 'text' => 'text-sky-600 dark:text-sky-400'],
        'lime'    => ['bg' => 'bg-lime-100 dark:bg-lime-500/10', 'text' => 'text-lime-600 dark:text-lime-400'],
        'gray'    => ['bg' => 'bg-gray-100 dark:bg-gray-500/10', 'text' => 'text-gray-600 dark:text-gray-400'],
    ];
    $currentColors = $colorClasses[$color] ?? $colorClasses['gray'];

    // For the change indicator, 'increase' is always green, 'decrease' is red/down
    $changeColorClass = match($changeType) {
        'increase' => 'text-green-500',
        'decrease' => 'text-red-500',
        default => 'text-gray-500 dark:text-gray-400',
    };
@endphp

{{-- The card now uses 'shadow' without a border and has corrected padding/margins --}}
<div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] transition-shadow hover:shadow-md">
    {{-- 1. Top Row: Title and Icon --}}
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $title }}</h3>
        <span class="p-2 rounded-full {{ $currentColors['bg'] }} {{ $currentColors['text'] }}">
            {{ $icon }}
        </span>
    </div>

    {{-- 2. Main Value --}}
    <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2 truncate">{{ $value }}</p>

    {{-- 3. Footer: Change indicator or static text --}}
    @if($change !== null || $footerText)
    <p class="text-sm mt-1 flex items-center {{ $change !== null ? $changeColorClass : 'text-gray-500 dark:text-gray-400' }}">
        {{-- Updated SVGs to be simple chevrons as in your screenshot --}}
        @if ($changeType === 'increase')
            <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
            </svg>
        @elseif ($changeType === 'decrease')
            {{-- Note: Your screenshot shows an "up" arrow for decrease, but typically a "down" arrow is used. I've used a down arrow for clarity. --}}
            <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
        @endif

        @if($change !== null)
            <span class="font-medium">{{ number_format(abs($change), 1) }}%</span>
            <span class="ml-1 text-gray-500 dark:text-gray-400">{{ $changeText }}</span>
        @else
            <span>{{ $footerText }}</span>
        @endif
    </p>
    @endif
</div>
