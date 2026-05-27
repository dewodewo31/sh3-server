<!-- SIDEBAR -->
<aside class="w-64 bg-black/40 backdrop-blur-xl border-r border-white/10 flex flex-col">

    <!-- Logo -->
    <div class="p-6 text-center border-b border-white/10">
        <div class="flex items-center justify-center gap-2 mb-1">
            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
            </svg>
            <h1 class="text-l font-bold tracking-widest text-green-400">SH3 - Admin Panel</h1>
        </div>
    </div>

    <!-- Menu -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">

        <!-- Dashboard -->
        <a href="{{ route('dashboard.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-green-500/20 hover:text-green-300 transition group {{ request()->routeIs('dashboard.index') ? 'bg-green-500/20 text-green-300' : '' }}">
            <svg class="w-5 h-5 text-gray-400 group-hover:text-green-300 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- Products Dropdown (Events + Merchandise) -->
        <div x-data="{ open: {{ request()->routeIs('events.*') || request()->routeIs('merchandise.index') ? 'true' : 'false' }} }" class="relative">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-lg hover:bg-green-500/20 hover:text-green-300 transition group">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-green-300 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M12 3a3.75 3.75 0 00-3.75 3.75h7.5A3.75 3.75 0 0012 3z"/>
                    </svg>
                    <span>Products</span>
                </div>
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>

            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 class="ml-9 mt-1 space-y-1">
                
                <!-- Events -->
                <a href="{{ route('events.index') }}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-500/20 hover:text-green-300 transition group text-sm {{ request()->routeIs('events.*') ? 'bg-green-500/20 text-green-300' : '' }}">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-green-300 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                    <span>Events</span>
                </a>

                <!-- Merchandise -->
                @if(auth()->user() && auth()->user()->role === 'admin')
                <a href="{{ route('merchandise.index') }}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-500/20 hover:text-green-300 transition group text-sm {{ request()->routeIs('merchandise.index') ? 'bg-green-500/20 text-green-300' : '' }}">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-green-300 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span>Merchandise</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Orders Dropdown (Event Orders + Merchandise Orders) -->
        <div x-data="{ open: {{ request()->routeIs('orders.*') || request()->routeIs('merchandise.orders*') ? 'true' : 'false' }} }" class="relative">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-lg hover:bg-green-500/20 hover:text-green-300 transition group">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-green-300 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.5c.36 0 .69.146.937.407M2.25 3h1.5c.36 0 .69.146.937.407M2.25 3L3 14.25M3.75 7.5h16.5M3.75 7.5L3 14.25m0 0h15.75M4.5 21h15a2.25 2.25 0 002.25-2.25V14.25m-16.5 0h16.5M6.75 18.75h.008v.008H6.75v-.008zm4.5 0h.008v.008h-.008v-.008zm4.5 0h.008v.008h-.008v-.008z"/>
                    </svg>
                    <span>Orders</span>
                </div>
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>

            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 class="ml-9 mt-1 space-y-1">
                
                <!-- Event Orders -->
                <a href="{{ route('orders.index') }}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-500/20 hover:text-green-300 transition group text-sm {{ request()->routeIs('orders.index') ? 'bg-green-500/20 text-green-300' : '' }}">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-green-300 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5h14a2 2 0 012 2v3a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2zm0 8h14a2 2 0 012 2v3a2 2 0 01-2 2H5a2 2 0 01-2-2v-3a2 2 0 012-2z"/>
                    </svg>
                    <span>Event Orders</span>
                </a>

                <!-- Merchandise Orders -->
                @if(auth()->user() && auth()->user()->role === 'admin')
                <a href="{{ route('merchandise.orders') }}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-500/20 hover:text-green-300 transition group text-sm {{ request()->routeIs('merchandise.orders*') ? 'bg-green-500/20 text-green-300' : '' }}">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-green-300 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span>Merchandise Orders</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Payments -->
        <a href="{{ route('payments.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-green-500/20 hover:text-green-300 transition group {{ request()->routeIs('payments.*') ? 'bg-green-500/20 text-green-300' : '' }}">
            <svg class="w-5 h-5 text-gray-400 group-hover:text-green-300 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
            </svg>
            <span>Payments</span>
        </a>

        <!-- Gallery -->
        <a href="{{ route('galleries.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-green-500/20 hover:text-green-300 transition group {{ request()->routeIs('galleries.*') ? 'bg-green-500/20 text-green-300' : '' }}">
            <svg class="w-5 h-5 text-gray-400 group-hover:text-green-300 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5H3.75A2.25 2.25 0 001.5 6.75v12A2.25 2.25 0 003.75 21zM14.25 8.25a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
            </svg>
            <span>Gallery</span>
        </a>

        <!-- Sponsors Menu (Admin Only) -->
        @if(auth()->user() && auth()->user()->role === 'admin')
        <a href="{{ route('sponsors.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-green-500/20 hover:text-green-300 transition group {{ request()->routeIs('sponsors.*') ? 'bg-green-500/20 text-green-300' : '' }}">
            <svg class="w-5 h-5 text-gray-400 group-hover:text-green-300 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span>Sponsors</span>
        </a>
        @endif

        <!-- Participants Menu (Admin Only) -->
        @if(auth()->user() && auth()->user()->role === 'admin')
            <div class="pt-4 mt-2">
                <p class="text-xs text-gray-500 px-4 mb-2 uppercase tracking-wider">Management</p>
                
                <a href="{{ route('participants.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-green-500/20 hover:text-green-300 transition group {{ request()->routeIs('participants.*') ? 'bg-green-500/20 text-green-300' : '' }}">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-green-300 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Participants</span>
                </a>
            </div>
        @endif

        <!-- Users Menu (Admin Only) -->
        @if(auth()->user() && auth()->user()->role === 'admin')
            <div>
                <a href="{{ route('users.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-green-500/20 hover:text-green-300 transition group {{ request()->routeIs('users.*') ? 'bg-green-500/20 text-green-300' : '' }}">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-green-300 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    <span>Users Management</span>
                </a>
            </div>
        @endif

        <!-- Role Info -->
        <div class="pt-4 mt-2">
            <div class="p-3 bg-white/5 rounded-lg text-xs text-gray-300 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
                <span>Login as: <span class="text-green-400 font-semibold">{{ auth()->user()->role ?? 'Guest' }}</span></span>
            </div>
        </div>

    </nav>

    <!-- Logout -->
    <div class="p-4 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full flex items-center justify-center gap-2 bg-red-500/20 hover:bg-red-500/40 text-red-300 py-2.5 rounded-lg transition group">
                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>

<!-- Add Alpine.js for dropdown functionality -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>