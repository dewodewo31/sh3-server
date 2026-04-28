<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-br from-gray-950 via-gray-900 to-gray-800 text-white min-h-screen flex">

    @include('layouts.sidebar')

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col">

        @include('layouts.navbar')

        <!-- CONTENT -->
        <section class="p-6 flex-1">

            <!-- STATS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0-2.25h.008v.008H12v-.008z"/>
                        </svg>
                        <p class="text-gray-400 text-sm">Total Events</p>
                    </div>
                    <h3 class="text-2xl font-bold text-green-400">0</h3>
                </div>

                <div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z"/>
                        </svg>
                        <p class="text-gray-400 text-sm">Total Orders</p>
                    </div>
                    <h3 class="text-2xl font-bold text-green-400">0</h3>
                </div>

                <div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-400 text-sm">Revenue</p>
                    </div>
                    <h3 class="text-2xl font-bold text-green-400">Rp 0</h3>
                </div>

            </div>

            <!-- CONTENT SLOT -->
            <div class="mt-6 bg-white/5 p-6 rounded-xl border border-white/10">
                @yield('content')
            </div>

        </section>

        @include('layouts.footer')

    </main>

</body>
</html>