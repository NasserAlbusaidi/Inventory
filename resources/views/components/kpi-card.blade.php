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
    $colorClasses = [
        'blue'    => ['bg' => 'bg-blue-100 dark:bg-blue-900/50', 'text' => 'text-blue-600 dark:text-blue-400'],
        'orange'  => ['bg' => 'bg-orange-100 dark:bg-orange-900/50', 'text' => 'text-orange-600 dark:text-orange-400'],
        'green'   => ['bg' => 'bg-green-100 dark:bg-green-900/50', 'text' => 'text-green-600 dark:text-green-400'],
        'purple'  => ['bg' => 'bg-purple-100 dark:bg-purple-900/50', 'text' => 'text-purple-600 dark:text-purple-400'],
        'indigo'  => ['bg' => 'bg-indigo-100 dark:bg-indigo-900/50', 'text' => 'text-indigo-600 dark:text-indigo-400'],
        'pink'    => ['bg' => 'bg-pink-100 dark:bg-pink-900/50', 'text' => 'text-pink-600 dark:text-pink-400'],
        'cyan'    => ['bg' => 'bg-cyan-100 dark:bg-cyan-900/50', 'text' => 'text-cyan-600 dark:text-cyan-400'],
        'teal'    => ['bg' => 'bg-teal-100 dark:bg-teal-900/50', 'text' => 'text-teal-600 dark:text-teal-400'],
        'yellow'  => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/50', 'text' => 'text-yellow-600 dark:text-yellow-400'],
        'red'     => ['bg' => 'bg-red-100 dark:bg-red-900/50', 'text' => 'text-red-600 dark:text-red-400'],
        'sky'     => ['bg' => 'bg-sky-100 dark:bg-sky-900/50', 'text' => 'text-sky-600 dark:text-sky-400'],
        'lime'    => ['bg' => 'bg-lime-100 dark:bg-lime-900/50', 'text' => 'text-lime-600 dark:text-lime-400'],
        'gray'    => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-600 dark:text-gray-400'],
    ];
    $currentColors = $colorClasses[$color] ?? $colorClasses['gray'];

    $changeColorClass = match($changeType) {
        'increase' => 'text-green-500',
        'decrease' => 'text-red-500',
        default => 'text-gray-500 dark:text-gray-400',
    };
@endphp

<div
    class="relative p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300">
    <div class="flex justify-between items-start">
        <div class="flex-1">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ $title }}</h3>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1 truncate">{{ $value }}</p>
        </div>
        <div class="flex-shrink-0 p-2 rounded-full {{ $currentColors['bg'] }}">
            <div class="{{ $currentColors['text'] }}">
                {{ $icon }}
            </div>
        </div>
    </div>
    @if($change !== null || $footerText)
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-4 flex items-center">
            @if ($change !== null)
                <span class="flex items-center mr-2 {{ $changeColorClass }}">
                    @if ($changeType === 'increase')
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7">
                            </path>
                        </svg>
                    @elseif ($changeType === 'decrease')
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    @endif
                    <span>{{ number_format(abs($change), 1) }}%</span>
                </span>
                <span>{{ $changeText }}</span>
            @else
                <span>{{ $footerText }}</span>
            @endif
        </div>
    @endif
</div>
