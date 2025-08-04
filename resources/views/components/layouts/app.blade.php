<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'DTT by Logix - Livewire Test' }}</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto py-8">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-center text-blue-600">DTT by Logix</h1>
            <p class="text-center text-gray-600 mt-2">Livewire Configuration Test</p>
        </header>
        
        <main>
            {{ $slot }}
        </main>
    </div>
    
    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>