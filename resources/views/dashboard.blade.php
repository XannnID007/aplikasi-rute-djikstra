@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <!-- Welcome Card -->
        <div
            class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl shadow-xl p-8 text-white relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-32 h-32 bg-white rounded-full -translate-x-16 -translate-y-16"></div>
                <div class="absolute bottom-0 right-0 w-48 h-48 bg-white rounded-full translate-x-16 translate-y-16"></div>
            </div>

            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-3">
                        <i class="fas fa-hand-wave mr-2 animate-pulse"></i>
                        Selamat Datang, {{ auth()->user()->name }}!
                    </h1>
                    <p class="text-blue-100 text-lg">
                        Siap mencari rute terpendek? Mari kita mulai perjalanan Anda.
                    </p>
                </div>
                <div class="text-right hidden md:block">
                    <div class="text-4xl mb-3">
                        <i class="fas fa-user-circle opacity-80"></i>
                    </div>
                    <p class="text-sm text-blue-100">{{ date('d F Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
                class="group bg-white rounded-2xl shadow-lg p-8 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div
                        class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 rounded-2xl mr-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-search text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-1">Cari Rute Baru</h3>
                        <p class="text-gray-600">Temukan jalur terpendek ke tujuan Anda</p>
                    </div>
                </div>
                <a href="{{ route('pencarian-rute.index') }}"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-3 px-6 rounded-xl transition-all duration-200 inline-flex items-center justify-center font-medium group-hover:shadow-lg">
                    <i class="fas fa-arrow-right mr-2 group-hover:translate-x-1 transition-transform"></i>
                    Mulai Pencarian
                </a>
            </div>

            <div
                class="group bg-white rounded-2xl shadow-lg p-8 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div
                        class="bg-gradient-to-r from-purple-500 to-purple-600 p-4 rounded-2xl mr-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-history text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-1">Riwayat Pencarian</h3>
                        <p class="text-gray-600">Lihat pencarian yang pernah dilakukan</p>
                    </div>
                </div>
                <a href="{{ route('pencarian-rute.riwayat') }}"
                    class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white py-3 px-6 rounded-xl transition-all duration-200 inline-flex items-center justify-center font-medium group-hover:shadow-lg">
                    <i class="fas fa-arrow-right mr-2 group-hover:translate-x-1 transition-transform"></i>
                    Lihat Riwayat
                </a>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                <div
                    class="bg-gradient-to-r from-green-400 to-green-500 p-4 rounded-2xl w-16 h-16 mx-auto mb-4 flex items-center justify-center shadow-lg">
                    <i class="fas fa-route text-2xl text-white"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Algoritma Dijkstra</h3>
                <p class="text-gray-600 text-sm">Menggunakan algoritma terbukti untuk menemukan jalur terpendek</p>
            </div>

            <div
                class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                <div
                    class="bg-gradient-to-r from-blue-400 to-blue-500 p-4 rounded-2xl w-16 h-16 mx-auto mb-4 flex items-center justify-center shadow-lg">
                    <i class="fas fa-map text-2xl text-white"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Peta Interaktif</h3>
                <p class="text-gray-600 text-sm">Visualisasi hasil dengan peta OpenStreetMap yang responsif</p>
            </div>

            <div
                class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                <div
                    class="bg-gradient-to-r from-orange-400 to-orange-500 p-4 rounded-2xl w-16 h-16 mx-auto mb-4 flex items-center justify-center shadow-lg">
                    <i class="fas fa-clock text-2xl text-white"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Estimasi Akurat</h3>
                <p class="text-gray-600 text-sm">Dapatkan estimasi jarak dan waktu tempuh yang tepat</p>
            </div>
        </div>
    </div>
@endsection
