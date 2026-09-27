<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RW Digital') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased h-full bg-slate-50 text-slate-800">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen flex bg-slate-50">
        <!-- Sidebar Navigation -->
        @include('layouts.sidebar')

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar Header -->
            @include('layouts.topbar')

            <!-- Main Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Page Header Title -->
                @if (isset($header))
                    <div class="mb-6">
                        {{ $header }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
