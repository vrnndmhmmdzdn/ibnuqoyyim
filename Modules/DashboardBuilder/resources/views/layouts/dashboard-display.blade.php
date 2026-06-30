<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle ?? config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    {{ $slot }}

    @livewireScripts

    <script type="module">
        import dashboardChart from '{{ asset('js/filament/widgets/components/chart.js') }}';

        const registerDashboardChart = () => {
            Alpine.data('dashboardStandaloneChart', ({ data, options, type }) => {
                return dashboardChart({
                    cachedData: data,
                    options,
                    type,
                });
            });
        };

        if (window.Alpine) {
            registerDashboardChart();
        } else {
            document.addEventListener('alpine:init', registerDashboardChart, { once: true });
        }
    </script>
</body>
</html>
