<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Pencarian Rute Terpendek</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-gray-200 fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-2 rounded-xl">
                            <i class="fas fa-route text-xl text-white"></i>
                        </div>
                        <span
                            class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            Sistem Rute Terpendek
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        @if (auth()->user()->apakahAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                                class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors duration-200">
                                <i class="fas fa-cog mr-1"></i>
                                Admin Panel
                            </a>
                        @endif
                        <a href="{{ route('pencarian-rute.index') }}"
                            class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-2 rounded-full font-medium transition-all duration-200 transform hover:scale-105">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors duration-200">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}"
                            class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-2 rounded-full font-medium transition-all duration-200 transform hover:scale-105">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Background Animation -->
        <div class="absolute inset-0 overflow-hidden">
            <div
                class="absolute -top-10 -left-10 w-80 h-80 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob">
            </div>
            <div
                class="absolute -top-10 -right-10 w-80 h-80 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000">
            </div>
            <div
                class="absolute -bottom-10 left-20 w-80 h-80 bg-indigo-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000">
            </div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 text-center px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">

            <!-- Heading -->
            <h1 class="text-5xl md:text-7xl font-bold text-gray-900 mb-6 leading-tight">
                Temukan Rute
                <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent">
                    Terpendek
                </span>
            </h1>

            <!-- Subtitle -->
            <p class="text-xl md:text-2xl text-gray-600 mb-12 max-w-3xl mx-auto leading-relaxed">
                Sistem pencarian rute terpendek menggunakan
                <span class="font-semibold text-blue-600">Algoritma Dijkstra</span>.
                Temukan jalur optimal dengan cepat dan akurat.
            </p>

        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <div class="w-6 h-10 border-2 border-gray-400 rounded-full flex justify-center">
                <div class="w-1 h-3 bg-gray-400 rounded-full mt-2 animate-pulse"></div>
            </div>
        </div>
    </div>

    <!-- Demo Section (Optional) -->
    <div class="relative bg-white/50 backdrop-blur-sm py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">
                Mudah Digunakan dalam 3 Langkah
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                <div class="relative">
                    <div
                        class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full w-12 h-12 flex items-center justify-center text-xl font-bold mx-auto mb-4 shadow-lg">
                        1
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Pilih Lokasi</h3>
                    <p class="text-gray-600">Tentukan titik asal dan tujuan perjalanan Anda</p>
                </div>

                <div class="relative">
                    <div
                        class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-full w-12 h-12 flex items-center justify-center text-xl font-bold mx-auto mb-4 shadow-lg">
                        2
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Klik Cari</h3>
                    <p class="text-gray-600">Algoritma Dijkstra akan menghitung rute optimal</p>
                </div>

                <div class="relative">
                    <div
                        class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-full w-12 h-12 flex items-center justify-center text-xl font-bold mx-auto mb-4 shadow-lg">
                        3
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Lihat Hasil</h3>
                    <p class="text-gray-600">Dapatkan rute terpendek dengan visualisasi peta</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900/90 backdrop-blur-sm text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-3 mb-4 md:mb-0">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-2 rounded-xl">
                        <i class="fas fa-route text-lg text-white"></i>
                    </div>
                    <span class="text-lg font-bold">Sistem Rute Terpendek</span>
                </div>

                <div class="text-center md:text-right">
                    <p class="text-gray-300 text-sm">
                        &copy; {{ date('Y') }} Sistem Rute Terpendek.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <style>
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }

            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Glass effect */
        .backdrop-blur-sm {
            backdrop-filter: blur(8px);
        }
    </style>
</body>

</html>
