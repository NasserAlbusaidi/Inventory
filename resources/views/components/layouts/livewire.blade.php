<!DOCTYPE html>
<!-- add dark class here when active -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
   <!-- ... head content ... -->
     @vite(['resources/css/app.css', 'resources/js/app.js'])
     <style> [x-cloak] { display: none !important; } </style> {{-- good practice for alpine --}}
      @livewireStyles
</head>
<!-- CHANGE background and text color -->
<body class="font-sans antialiased bg-gray-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 transition-colors duration-300">
    <!-- Ensure your navigation also has dark: classes -->
    <div class="min-h-screen ">
         @include('layouts.partials.navigation')
        <!-- Add some padding to the main container -->
        <main class="p-4 sm:p-8">
             {{ $slot }}
        </main>
    </div>
     @livewireScripts
     @stack('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
