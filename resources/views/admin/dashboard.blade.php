@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}!</h1>
                    <p class="text-blue-100">Kelola sistem pencarian rute terpendek dengan mudah</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl mb-2">
                        <i class="fas fa-route"></i>
                    </div>
                    <p class="text-sm text-blue-100">{{ date('d F Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Pengguna -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Pengguna</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $data['total_pengguna'] }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.pengguna') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Total Lokasi -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Lokasi</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $data['total_lokasi'] }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-map-marker-alt text-2xl text-green-600"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.lokasi.index') }}"
                        class="text-green-600 hover:text-green-700 text-sm font-medium">
                        Kelola Lokasi <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Total Pencarian -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Pencarian</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $data['total_pencarian'] }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <i class="fas fa-search text-2xl text-yellow-600"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.riwayat-pencarian') }}"
                        class="text-yellow-600 hover:text-yellow-700 text-sm font-medium">
                        Lihat Riwayat <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Pencarian Hari Ini -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Pencarian Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $data['pencarian_hari_ini'] }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-calendar-day text-2xl text-purple-600"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-purple-600 text-sm font-medium">
                        Aktivitas Hari Ini
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Quick Actions Card -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-bolt text-blue-600 mr-2"></i>
                    Aksi Cepat
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.lokasi.create') }}"
                        class="flex items-center p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200">
                        <div class="bg-blue-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-plus text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Tambah Lokasi Baru</p>
                            <p class="text-sm text-gray-600">Tambahkan lokasi untuk sistem</p>
                        </div>
                    </a>

                    <a href="{{ route('pencarian-rute.index') }}"
                        class="flex items-center p-3 rounded-lg border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors duration-200">
                        <div class="bg-green-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-route text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Coba Pencarian Rute</p>
                            <p class="text-sm text-gray-600">Test fitur pencarian rute</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- System Info Card -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-info-circle text-green-600 mr-2"></i>
                    Informasi Sistem
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">Algoritma</span>
                        <span class="text-sm text-gray-900 font-semibold">Dijkstra</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">Peta</span>
                        <span class="text-sm text-gray-900 font-semibold">OpenStreetMap</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">Framework</span>
                        <span class="text-sm text-gray-900 font-semibold">Laravel 10</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">Status Sistem</span>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-circle text-green-500 mr-1 text-xs"></i>
                            Aktif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
