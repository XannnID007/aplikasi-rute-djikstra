<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Admin Dashboard') - Sistem Rute Terpendek</title>
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-blue-800 to-blue-900 shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0">
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 bg-blue-900 border-b border-blue-700">
                <h1 class="text-white text-lg font-bold">
                    <i class="fas fa-route mr-2"></i>
                    Admin Panel
                </h1>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center px-4 py-3 text-white hover:bg-blue-700 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    
                    <a href="{{ route('admin.lokasi.index') }}" 
                       class="flex items-center px-4 py-3 text-white hover:bg-blue-700 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.lokasi.*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-map-marker-alt mr-3"></i>
                        Kelola Lokasi
                    </a>
                    
                    <a href="{{ route('admin.pengguna') }}" 
                       class="flex items-center px-4 py-3 text-white hover:bg-blue-700 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.pengguna') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-users mr-3"></i>
                        Daftar Pengguna
                    </a>
                    
                    <a href="{{ route('admin.riwayat-pencarian') }}" 
                       class="flex items-center px-4 py-3 text-white hover:bg-blue-700 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.riwayat-pencarian') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-history mr-3"></i>
                        Riwayat Pencarian
                    </a>
                </div>
                
                <!-- Divider -->
                <div class="my-6 mx-4 border-t border-blue-700"></div>
                
                <div class="px-4 space-y-2">
                    <a href="{{ route('pencarian-rute.index') }}" 
                       class="flex items-center px-4 py-3 text-white hover:bg-blue-700 rounded-lg transition-colors duration-200">
                        <i class="fas fa-search mr-3"></i>
                        Pencarian Rute
                    </a>
                </div>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 lg:ml-64">
            <!-- Top Navigation -->
            <nav class="fixed top-0 right-0 left-0 lg:left-64 z-40 bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <button class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                                <i class="fas fa-bars"></i>
                            </button>
                            <h2 class="ml-2 text-xl font-semibold text-gray-800">
                                @yield('page-title', 'Dashboard')
                            </h2>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="flex items-center space-x-4">
                            <div class="relative">
                                <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" onclick="toggleDropdown()">
                                    <div class="bg-blue-600 text-white rounded-full h-8 w-8 flex items-center justify-center">
                                        <i class="fas fa-user text-sm"></i>
                                    </div>
                                    <span class="ml-2 text-gray-700">{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down ml-1 text-gray-500"></i>
                                </button>
                                
                                <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user-edit mr-2"></i>Profil
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Page Content -->
            <main class="pt-16 pb-6">
                <div class="px-6 py-6">
                    <!-- Alert Messages -->
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const button = event.target.closest('button');
            
            if (!button || !button.onclick) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>