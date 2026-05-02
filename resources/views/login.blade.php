<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-100 to-amber-100">

    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">

            <!-- Header -->
            <div class="bg-gradient-to-r from-green-700 to-amber-700 p-6 text-center">
                <h1 class="text-2xl font-bold text-white">Run Event</h1>
                <p class="text-sm text-green-100">Masuk ke dashboard</p>
            </div>

            <!-- Form -->
            <div class="p-6">
                <form method="POST" action="{{ route('login.auth') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Email
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-600 focus:outline-none"
                            required
                        >
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Password
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-600 focus:outline-none"
                            required
                        >
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember -->
                    <div class="flex items-center justify-between mb-4">
                        <label class="flex items-center text-sm text-gray-600">
                            <input type="checkbox" name="remember" class="mr-2">
                            Remember me
                        </label>
                    </div>

                    <!-- Button -->
                    <button 
                        type="submit"
                        class="w-full bg-green-700 hover:bg-green-800 text-white py-2 rounded-lg font-semibold transition duration-200"
                    >
                        Login
                    </button>

                </form>
            </div>

        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-gray-500 mt-4">
            © {{ date('Y') }} Run Event. All rights reserved.
        </p>
    </div>

</body>
</html>