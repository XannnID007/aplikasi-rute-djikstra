<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Pencarian Rute') - Sistem Rute Terpendek</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200 fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-route text-2xl text-blue-600 mr-2"></i>
                        <span class="text-xl font-bold text-gray-900">Sistem Rute Terpendek</span>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:ml-8 md:flex md:space-x-8">
                        <a href="{{ route('pencarian-rute.index') }}"
                            class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('pencarian-rute.index') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                            <i class="fas fa-search mr-1"></i>
                            Pencarian Rute
                        </a>
                        <a href="{{ route('pencarian-rute.riwayat') }}"
                            class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('pencarian-rute.riwayat') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                            <i class="fas fa-history mr-1"></i>
                            Riwayat
                        </a>
                        @if (auth()->user()->apakahAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                                class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors duration-200">
                                <i class="fas fa-cog mr-1"></i>
                                Admin Panel
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Right side -->
                <div class="flex items-center space-x-4">
                    <!-- User Menu -->
                    <div class="relative">
                        <button
                            class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onclick="toggleUserDropdown()">
                            <div class="bg-blue-600 text-white rounded-full h-8 w-8 flex items-center justify-center">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <span class="ml-2 text-gray-700 hidden md:block">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down ml-1 text-gray-500 hidden md:block"></i>
                        </button>

                        <div id="userDropdown"
                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <div class="px-4 py-2 border-b border-gray-100 md:hidden">
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user-edit mr-2"></i>Profil
                            </a>
                            <a href="{{ route('pencarian-rute.riwayat') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 md:hidden">
                                <i class="fas fa-history mr-2"></i>Riwayat
                            </a>
                            @if (auth()->user()->apakahAdmin())
                                <a href="{{ route('admin.dashboard') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 md:hidden">
                                    <i class="fas fa-cog mr-2"></i>Admin Panel
                                </a>
                            @endif
                            <div class="border-t border-gray-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Keluar
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button class="text-gray-700 hover:text-blue-600 p-2" onclick="toggleMobileMenu()">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-gray-200">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('pencarian-rute.index') }}"
                    class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md {{ request()->routeIs('pencarian-rute.index') ? 'text-blue-600 bg-blue-50' : '' }}">
                    <i class="fas fa-search mr-2"></i>
                    Pencarian Rute
                </a>
                <a href="{{ route('pencarian-rute.riwayat') }}"
                    class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md {{ request()->routeIs('pencarian-rute.riwayat') ? 'text-blue-600 bg-blue-50' : '' }}">
                    <i class="fas fa-history mr-2"></i>
                    Riwayat
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Alert Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-gray-600">
                <p>&copy; {{ date('Y') }} Sistem Rute Terpendek.
                </p>
            </div>
        </div>
    </footer>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const userDropdown = document.getElementById('userDropdown');
            const userButton = event.target.closest('[onclick="toggleUserDropdown()"]');

            if (!userButton) {
                userDropdown.classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
