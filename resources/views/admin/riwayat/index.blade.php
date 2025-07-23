@extends('layouts.admin')

@section('title', 'Riwayat Pencarian')
@section('page-title', 'Riwayat Pencarian')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Riwayat Pencarian</h1>
                <p class="text-gray-600 mt-1">Semua riwayat pencarian rute yang dilakukan pengguna</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Pencarian</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $riwayat->total() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-search text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Hari Ini</p>
                        <p class="text-3xl font-bold text-green-600">
                            {{ \App\Models\PencarianRute::whereDate('created_at', today())->count() }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-calendar-day text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Minggu Ini</p>
                        <p class="text-3xl font-bold text-purple-600">
                            {{ \App\Models\PencarianRute::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}
                        </p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-calendar-week text-2xl text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Rata-rata/Hari</p>
                        <p class="text-3xl font-bold text-orange-600">
                            {{ round(\App\Models\PencarianRute::count() / max(1, \App\Models\PencarianRute::selectRaw('DATE(created_at) as date')->distinct()->count())) }}
                        </p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <i class="fas fa-chart-line text-2xl text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-filter text-blue-600 mr-2"></i>
                    Filter Pencarian
                </h3>

                <div class="flex flex-col sm:flex-row gap-3">
                    <select
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>Semua Pengguna</option>
                        <option>Hari Ini</option>
                        <option>Minggu Ini</option>
                        <option>Bulan Ini</option>
                    </select>

                    <select
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>Semua Lokasi</option>
                        @foreach (\App\Models\Lokasi::all() as $lokasi)
                            <option>{{ $lokasi->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Riwayat Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Data Riwayat Pencarian</h3>
            </div>

            @if ($riwayat->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pengguna</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rute</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jarak</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($riwayat as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $riwayat->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="bg-blue-600 text-white rounded-full h-8 w-8 flex items-center justify-center mr-3">
                                                <span
                                                    class="text-xs font-medium">{{ strtoupper(substr($item->user->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $item->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $item->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center text-sm">
                                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                                <span class="font-medium text-gray-900">{{ $item->lokasiAsal->nama }}</span>
                                            </div>
                                            <div class="flex items-center text-sm">
                                                <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                                <span
                                                    class="font-medium text-gray-900">{{ $item->lokasiTujuan->nama }}</span>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ count($item->jalur_rute) }} titik perjalanan
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $item->total_jarak }} km
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $item->total_waktu }} menit
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>{{ $item->created_at->format('d/m/Y H:i') }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->created_at->diffForHumans() }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $riwayat->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="bg-gray-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-history text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada riwayat pencarian</h3>
                    <p class="text-gray-600">Riwayat pencarian akan muncul di sini setelah pengguna melakukan pencarian rute
                    </p>
                </div>
            @endif
        </div>

        <!-- Popular Routes -->
        @if ($riwayat->count() > 0)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-star text-yellow-600 mr-2"></i>
                    Rute Paling Populer
                </h3>

                @php
                    $popularRoutes = \App\Models\PencarianRute::with(['lokasiAsal', 'lokasiTujuan'])
                        ->selectRaw('lokasi_asal_id, lokasi_tujuan_id, COUNT(*) as count')
                        ->groupBy('lokasi_asal_id', 'lokasi_tujuan_id')
                        ->orderBy('count', 'desc')
                        ->limit(5)
                        ->get();
                @endphp

                <div class="space-y-3">
                    @foreach ($popularRoutes as $route)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                    <i class="fas fa-route text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">
                                        {{ $route->lokasiAsal->nama }} → {{ $route->lokasiTujuan->nama }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ $route->count }} kali pencarian
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    #{{ $loop->iteration }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
