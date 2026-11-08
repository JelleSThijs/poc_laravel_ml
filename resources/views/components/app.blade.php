<!doctype html>
<html lang="en" class="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>
        {{ $title ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
    </title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-stone-900">
<header class="sticky top-0 z-30 border-b border-stone-200 bg-white/95 shadow-sm backdrop-blur">
    <nav class="mx-auto flex min-h-16 w-full max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8" aria-label="Main navigation">
        <a href="{{ route('home') }}" class="text-lg font-bold tracking-tight text-emerald-900">
            FruitMatch
        </a>
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="{{ route('preferences') }}"
                @class([
                    'inline-flex min-h-10 items-center rounded-xl px-3 text-sm font-semibold transition sm:px-4',
                    'bg-emerald-100 text-emerald-900' => request()->routeIs('preferences'),
                    'text-stone-700 hover:bg-stone-100' => ! request()->routeIs('preferences'),
                ])>
                My preferences
            </a>
            <a href="{{ route('fruits.create') }}"
                @class([
                    'inline-flex min-h-10 items-center rounded-xl border px-3 text-sm font-semibold transition sm:px-4',
                    'border-emerald-700 bg-emerald-700 text-white hover:bg-emerald-800' => request()->routeIs('fruits.create'),
                    'border-emerald-200 bg-white text-emerald-800 hover:bg-emerald-50' => ! request()->routeIs('fruits.create'),
                ])>
                New fruit
            </a>
        </div>
    </nav>
</header>

{{ $slot }}

@livewireScripts
@fluxScripts
</body>
</html>
