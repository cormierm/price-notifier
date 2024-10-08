<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @yield('head')

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/vue.js'])
    @inertiaHead
</head>

<body class="font-sans antialiased dark:bg-black dark:text-gray-100">
    <div class="min-h-screen">
        @include('layouts.navigation')

        @isset($header)
            <header class="bg-white shadow dark:bg-black dark:text-gray-200">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main>
            @inertia
        </main>
    </div>
</body>
</html>
