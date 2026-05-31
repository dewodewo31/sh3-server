<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Event Management</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #eventMap, #previewMap { height: 300px; border-radius: 0.5rem; z-index: 1; }
        .leaflet-popup-content { min-width: 200px; }
        .leaflet-popup-content p { margin: 5px 0; }
        
        /* Mobile menu styles */
        .mobile-menu-overlay {
            transition: opacity 0.3s ease;
        }
        
        @media (max-width: 1024px) {
            body.sidebar-open {
                overflow: hidden;
            }
        }
    </style>
    
    <!-- Vite CSS -->
    @vite('resources/css/app.css')
    
    <!-- Additional CSS -->
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-gray-950 via-gray-900 to-gray-800 text-white min-h-screen lg:flex">
    
    <!-- Mobile Menu Toggle Button -->
    <button id="mobileMenuToggle" class="fixed top-4 left-4 z-50 lg:hidden bg-green-500/20 backdrop-blur rounded-lg p-2 border border-green-500/30 shadow-lg">
        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
        </svg>
    </button>

    <!-- Mobile Menu Overlay -->
    <div id="mobileMenuOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden lg:hidden mobile-menu-overlay"></div>

    <!-- SIDEBAR -->
    @include('layouts.sidebar')

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col w-full min-h-screen">
        <!-- NAVBAR -->
        @include('layouts.navbar')

        <!-- PAGE CONTENT -->
        <section class="p-4 sm:p-6 flex-1">
            <!-- STATS CARDS - Only show on dashboard/index page -->
            @if(View::hasSection('stats'))
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-4 sm:mb-6">
                    @yield('stats')
                </div>
            @endif

            <!-- BREADCRUMB (Optional) -->
            @if(View::hasSection('breadcrumb'))
                <div class="mb-3 sm:mb-4">
                    @yield('breadcrumb')
                </div>
            @endif

            <!-- MAIN CONTENT SLOT -->
            <div class="bg-white/5 rounded-xl border border-white/10 p-4 sm:p-6">
                @yield('content')
            </div>
        </section>

        <!-- FOOTER -->
        @include('layouts.footer')
    </main>

    <!-- Scripts -->
    @vite('resources/js/app.js')
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Alpine.js for dropdown -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
            const sidebar = document.getElementById('sidebar');
            const body = document.body;
            
            function openSidebar() {
                if (sidebar) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    if (mobileMenuOverlay) {
                        mobileMenuOverlay.classList.remove('hidden');
                        mobileMenuOverlay.classList.add('block');
                    }
                    body.classList.add('sidebar-open');
                }
            }
            
            function closeSidebar() {
                if (sidebar) {
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                    if (mobileMenuOverlay) {
                        mobileMenuOverlay.classList.remove('block');
                        mobileMenuOverlay.classList.add('hidden');
                    }
                    body.classList.remove('sidebar-open');
                }
            }
            
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', openSidebar);
            }
            
            if (mobileMenuOverlay) {
                mobileMenuOverlay.addEventListener('click', closeSidebar);
            }
            
            // Close sidebar on window resize if screen becomes large
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    closeSidebar();
                    if (sidebar) {
                        sidebar.classList.remove('-translate-x-full', 'translate-x-0');
                    }
                }
            });
            
            // Close sidebar when clicking escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && body.classList.contains('sidebar-open')) {
                    closeSidebar();
                }
            });
        });
    </script>
    
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