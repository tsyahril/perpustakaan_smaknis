<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perpustakaan SMAKNIS - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex min-h-screen" x-data="{ collapsed: false, mobileOpen: false }">
        <div x-show="mobileOpen" x-transition.opacity @click="mobileOpen = false" style="display: none;" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden"></div>

        @include('components.sidebar')

        <div class="flex-1 flex flex-col min-w-0">
            @include('components.topbar')

            <main class="flex-1 p-4 md:p-6">
                @yield('content')
            </main>

            @include('components.footer')
        </div>
    </div>

    @include('components.swal')
</body>
</html>
