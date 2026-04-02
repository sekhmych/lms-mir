<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex" style="background-color: #C7EDFF;">
            @include('layouts.navigation')

            <div class="flex-1 flex flex-col min-w-0">
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow">
                        <div class="py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1">
                    {{ $slot }}
                </main>

                <!-- Footer -->
                <footer class="bg-white text-gray-700 mt-auto rounded-t-2xl shadow">
                    <div class="px-4 sm:px-6 lg:px-8 py-6">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex flex-wrap gap-6 text-sm">
                                <a href="#" class="hover:text-gray-300 transition-colors">Горячая линия</a>
                                <a href="#" class="hover:text-gray-300 transition-colors">Документы</a>
                                <a href="#" class="hover:text-gray-300 transition-colors">Карта сайта</a>
                            </div>
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('logo.png') }}" alt="Logo" class="h-8 w-auto" />
                                <img src="{{ asset('logo2.png') }}" alt="Logo 2" class="h-8 w-auto" />
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>
