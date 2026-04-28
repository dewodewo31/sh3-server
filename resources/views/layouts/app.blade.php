<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Event Management</title>
    
    <!-- Vite CSS -->
    @vite('resources/css/app.css')
    
    <!-- Additional CSS -->
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-gray-950 via-gray-900 to-gray-800 text-white min-h-screen flex">

    <!-- SIDEBAR -->
    @include('layouts.sidebar')

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col">

        <!-- NAVBAR -->
        @include('layouts.navbar')

        <!-- PAGE CONTENT -->
        <section class="p-6 flex-1">

            <!-- STATS CARDS - Only show on dashboard/index page -->
            @if(View::hasSection('stats'))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    @yield('stats')
                </div>
            @endif

            <!-- BREADCRUMB (Optional) -->
            @if(View::hasSection('breadcrumb'))
                <div class="mb-4">
                    @yield('breadcrumb')
                </div>
            @endif

            <!-- MAIN CONTENT SLOT -->
            <div class="bg-white/5 rounded-xl border border-white/10 p-6">
                @yield('content')
            </div>

        </section>

        <!-- FOOTER -->
        @include('layouts.footer')

    </main>

    <!-- Scripts -->
    @vite('resources/js/app.js')
    
    <!-- Additional Scripts -->
    @stack('scripts')
    
    <!-- Flash Message Auto Hide -->
    @if(session('success'))
    <script>
        setTimeout(function() {
            const alert = document.querySelector('.alert-success');
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>
    @endif
    
    @if(session('error'))
    <script>
        setTimeout(function() {
            const alert = document.querySelector('.alert-error');
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 3000);
            }
        }, 3000);
    </script>
    @endif
</body>
</html>