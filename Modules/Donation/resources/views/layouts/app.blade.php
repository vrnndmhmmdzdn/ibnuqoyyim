<!doctype html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Donasi' }}</title>
        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900">
        <div class="min-h-screen bg-slate-100">
            <header class="border-b border-slate-200 bg-white">
                <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-3 px-4 py-4 sm:px-6 sm:py-5">
                    <a href="{{ route('donation.campaigns.index') }}" class="text-lg font-semibold tracking-wide text-slate-900">
                        DONASI
                    </a>
                    <a href="{{ route('donation.campaigns.index') }}" class="whitespace-nowrap rounded-full border border-orange-300 bg-orange-50 px-3 py-2 text-xs text-orange-700 hover:border-orange-400 sm:px-4 sm:text-sm">
                        Lihat Campaign
                    </a>
                </div>
            </header>

            <main class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 sm:py-8">
                {{ $slot }}
            </main>
        </div>
        @livewireScripts
    </body>
</html>
