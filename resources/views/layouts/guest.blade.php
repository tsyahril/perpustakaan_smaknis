<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Perpustakaan SMAKNIS') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased text-slate-800 bg-slate-100 selection:bg-blue-600 selection:text-white">
        <!-- Background Pattern / Decoration -->
        <div class="min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 bg-gradient-to-br from-slate-100 via-blue-50/50 to-slate-100">
            
            <!-- Auth Card Container -->
            <div class="w-full sm:max-w-md bg-white shadow-xl shadow-slate-200/60 rounded-xl overflow-hidden border border-slate-200/80 transition-all duration-300">
                <div class="p-6">
                    {{ $slot }}
                </div>
            </div>

            <!-- Footer Small Link/Copyright -->
            <div class="mt-6 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} Perpustakaan SMAKNIS. All rights reserved.
            </div>

        </div>
    </body>
</html>