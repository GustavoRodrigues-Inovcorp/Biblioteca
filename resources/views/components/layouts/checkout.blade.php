@props([
    'backUrl' => route('carrinho.index'),
    'brand' => 'INOVBOOKS',
    'secureLabel' => 'Checkout Seguro',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Checkout | {{ config('app.name') }}</title>
        <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-white font-sans text-slate-900 antialiased">
        <div class="flex min-h-screen flex-col">
            <header class="sticky top-0 z-50 bg-blue-800 text-white shadow-sm">
                <div class="mx-auto flex h-14 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <a href="{{ $backUrl }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-white transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/40" aria-label="Voltar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="h-7 w-7" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15 19-7-7 7-7" />
                        </svg>
                    </a>

                    <div class="flex flex-1 items-center justify-center px-3">
                        <span class="text-2xl font-bold uppercase">{{ $brand }}</span>
                    </div>

                    <div class="flex items-center text-[11px] font-light gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                            <path d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25V9H6a2.25 2.25 0 0 0-2.25 2.25v7.5A2.25 2.25 0 0 0 6 21h12a2.25 2.25 0 0 0 2.25-2.25v-7.5A2.25 2.25 0 0 0 18 9h-.75V6.75A5.25 5.25 0 0 0 12 1.5Zm-3 7.5V6.75a3 3 0 1 1 6 0V9h-6Z" />
                        </svg>
                        <span class="whitespace-nowrap">{{ $secureLabel }}</span>
                    </div>
                </div>
            </header>

            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
    </body>
</html>