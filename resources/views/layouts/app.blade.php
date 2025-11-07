<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - ' . config('app.name', 'EcoRide') : config('app.name', 'EcoRide') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 flex flex-col min-h-screen" x-data="{ open: false }">
    @include('layouts.partials.header')

    <!-- Page Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    @include('layouts.partials.footer')
    @include('layouts.partials.mobile-menu')

    @stack('scripts')

    @auth
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.TripNotificationManager) {
                    fetch('{{ route('api.user.todayTrips') }}')
                        .then(response => response.json())
                        .then(trips => {
                            const manager = new window.TripNotificationManager();
                            manager.init(trips, {{ auth()->id() }});

                            window.tripNotificationManager = manager;

                            const logoutForms = document.querySelectorAll('form[action*="logout"]');
                            logoutForms.forEach(form => {
                                form.addEventListener('submit', function() {
                                    if (window.tripNotificationManager) {
                                        window.tripNotificationManager.clearClosedNotifications();
                                    }
                                });
                            });
                        })
                        .catch(error => {
                            console.error('Erreur lors du chargement des notifications:', error);
                        });
                }
            });
        </script>
    @endauth
</body>

</html>
