<!DOCTYPE html>
{{-- add dark mode --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
   x-data="{
        darkMode: localStorage.getItem('darkMode') === 'true'
    }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode }">
    <x-slot name="title">
        {{ $title ?? 'Inventory' }}
    </x-slot>


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Inventory' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @livewireStyles
</head>

<body
    class="font-sans antialiased bg-gray-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 transition-colors duration-300">
    <div class="min-h-screen">
        @include('layouts.partials.navigation')

        <main class="p-4 sm:p-8">
            {{ $slot }}
        </main>
    </div>

    @livewire('wire-elements-modal') {{-- Add this line --}}

    @livewireScripts
    @stack('scripts')
     <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('tomSelect', (config) => ({
                // The Livewire property model, e.g., 'items.0.selected_item_key'
                wireModel: config.wireModel,
                // The pre-loaded options from Livewire
                options: config.options,
                // The initial value
                initialValue: config.initialValue,
                // Tom Select instance
                select: null,

                init() {
                    this.select = new TomSelect(this.$refs.select, {
                        options: this.options,
                        items: [this.initialValue], // Set the initial selected item
                        valueField: 'key',
                        labelField: 'display_name',
                        searchField: ['display_name'],
                        create: false,
                        // When the user selects an item, update Livewire
                        onChange: (value) => {
                            this.$wire.set(this.wireModel, value);
                        },
                    });

                    // A listener to clear the select if the component is reset
                    this.$refs.select.addEventListener('clear', () => {
                       this.select.clear();
                    });

                    // Watch for Livewire updating the value externally
                    this.$watch('initialValue', (newValue, oldValue) => {
                        // Only update if the value is actually different
                        if (newValue !== this.select.getValue()) {
                            this.select.setValue(newValue, true); // silent = true to prevent onChange loop
                        }
                    });
                },

                destroy() {
                    // Clean up the Tom Select instance to prevent memory leaks
                    if (this.select) {
                        this.select.destroy();
                    }
                }
            }));
        });
    </script>
</body>

</html>
