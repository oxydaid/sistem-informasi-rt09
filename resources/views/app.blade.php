<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            $favicon = app(\App\Services\SettingsService::class)->get('site_favicon', '/favicon.svg');
        @endphp
        <link rel="icon" type="image/svg+xml" href="{{ $favicon }}">
        <link rel="alternate icon" href="/favicon.ico">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <style>
            /* Instant background to completely eliminate FOUC before JS hydration */
            html, body {
                background-color: #F5F7FA;
                margin: 0;
                padding: 0;
            }
            #app {
                min-height: 100vh;
                background-color: #F5F7FA;
            }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.ts'])
        <x-inertia::head>
            <title>{{ config('app.name', 'Sistem Informasi RT 09') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased bg-[#F5F7FA] text-[#1A1A2E]">
        @inertia
    </body>
</html>
