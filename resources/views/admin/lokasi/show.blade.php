@extends('layouts.admin')

@section('title', 'Detail Lokasi')
@section('page-title', 'Detail Lokasi')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Lokasi</h1>
                <p class="text-gray-600 mt-1">Informasi lengkap lokasi {{ $lokasi->nama }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.lokasi.edit', $lokasi) }}"
                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('admin.lokasi.index') }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informasi Lokasi -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Informasi Lokasi
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Nama Lokasi</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $lokasi->nama }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">Alamat</label>
                            <p class="text-gray-900">{{ $lokasi->alamat }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Latitude</label>
                                <p class="text-gray-900 font-mono">{{ $lokasi->latitude }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Longitude</label>
                                <p class="text-gray-900 font-mono">{{ $lokasi->longitude }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">Status</label>
                            <div class="mt-1">
                                @if ($lokasi->aktif)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-circle text-green-500 mr-1 text-xs"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-circle text-red-500 mr-1 text-xs"></i>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Dibuat</label>
                                <p class="text-sm text-gray-900">{{ $lokasi->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Diperbarui</label>
                                <p class="text-sm text-gray-900">{{ $lokasi->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Peta -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-map text-green-600 mr-2"></i>
                            Lokasi di Peta
                        </h3>
                    </div>

                    <div id="map" class="h-96"></div>

                    <div class="px-6 py-4 bg-gray-50">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                            Koordinat: {{ $lokasi->latitude }}, {{ $lokasi->longitude }}
                        </div>
                    </div>
                </div>

                <!-- Statistik Rute -->
                <div class="bg-white rounded-xl shadow-md p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-chart-bar text-purple-600 mr-2"></i>
                        Statistik Rute
                    </h3>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $lokasi->ruteAsal->count() }}</div>
                            <div class="text-sm text-gray-600">Rute Keluar</div>
                        </div>

                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $lokasi->ruteTujuan->count() }}</div>
                            <div class="text-sm text-gray-600">Rute Masuk</div>
                        </div>

                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">{{ $lokasi->pencarianRuteAsal->count() }}</div>
                            <div class="text-sm text-gray-600">Pencarian dari</div>
                        </div>

                        <div class="text-center p-4 bg-orange-50 rounded-lg">
                            <div class="text-2xl font-bold text-orange-600">{{ $lokasi->pencarianRuteTujuan->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Pencarian ke</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const lat = {{ $lokasi->latitude }};
                const lng = {{ $lokasi->longitude }};

                // Initialize map
                const map = L.map('map').setView([lat, lng], 15);

                // Add tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Add marker
                const marker = L.marker([lat, lng], {
                    icon: L.divIcon({
                        className: 'custom-marker',
                        html: '<div style="background-color: #3B82F6; width: 24px; height: 24px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-size: 12px;">📍</div>',
                        iconSize: [24, 24],
                        iconAnchor: [12, 12]
                    })
                }).addTo(map);

                // Add popup
                marker.bindPopup(`
        <div class="text-center">
            <h4 class="font-semibold">{{ $lokasi->nama }}</h4>
            <p class="text-sm text-gray-600">{{ $lokasi->alamat }}</p>
            <div class="text-xs text-gray-500 mt-1">
                Lat: {{ $lokasi->latitude }}<br>
                Lng: {{ $lokasi->longitude }}
            </div>
        </div>
    `).openPopup();
            });
        </script>
    @endpush
@endsection
