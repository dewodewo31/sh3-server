<!-- TOPBAR / NAVBAR -->
<header class="h-16 flex items-center justify-between px-6 border-b border-white/10 bg-black/20 backdrop-blur">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/>
        </svg>
        <h2 class="text-lg font-semibold">@yield('page_title', 'Dashboard')</h2>
    </div>

    <div class="flex items-center gap-2 text-sm text-gray-300">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span>Welcome, <span class="text-green-400 font-medium">{{ auth()->user()->name }}</span></span>
    </div>
</header>