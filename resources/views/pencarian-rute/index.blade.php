@extends('layouts.user')

@section('title', 'Pencarian Rute')

@section('content')
    <div class="space-y-6">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-8 text-white">
            <div class="text-center">
                <h1 class="text-3xl font-bold mb-2">
                    <i class="fas fa-route mr-3"></i>
                    Pencarian Rute Terpendek
                </h1>
                <p class="text-blue-100 text-lg">
                    Temukan rute tercepat dan terdekat menggunakan Algoritma Dijkstra
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Pencarian -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-md p-6 sticky top-20">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-search text-blue-600 mr-2"></i>
                        Cari Rute
                    </h3>

                    <form action="{{ route('pencarian-rute.cari') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Lokasi Asal -->
                        <div>
                            <label for="lokasi_asal" class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi Asal <span class="text-red-500">*</span>
                            </label>
                            <select id="lokasi_asal" name="lokasi_asal"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('lokasi_asal') border-red-500 @enderror"
                                required>
                                <option value="">Pilih lokasi asal</option>
                                @foreach ($lokasi as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('lokasi_asal') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('lokasi_asal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Lokasi Tujuan -->
                        <div>
                            <label for="lokasi_tujuan" class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi Tujuan <span class="text-red-500">*</span>
                            </label>
                            <select id="lokasi_tujuan" name="lokasi_tujuan"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('lokasi_tujuan') border-red-500 @enderror"
                                required>
                                <option value="">Pilih lokasi tujuan</option>
                                @foreach ($lokasi as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('lokasi_tujuan') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('lokasi_tujuan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-search mr-2"></i>
                            Cari Rute Terpendek
                        </button>
                    </form>

                    <!-- Info Algoritma -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-medium text-blue-900 mb-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Tentang Algoritma Dijkstra
                        </h4>
                        <p class="text-sm text-blue-800">
                            Algoritma Dijkstra adalah algoritma untuk menemukan jalur terpendek antara node dalam graf.
                            Sistem ini akan menghitung rute dengan jarak terpendek berdasarkan data yang tersedia.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Peta dan Informasi -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <!-- Header Peta -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-map text-green-600 mr-2"></i>
                            Peta Lokasi
                        </h3>
                    </div>

                    <!-- Peta -->
                    <div id="map" class="h-96"></div>

                    <!-- Legenda -->
                    <div class="px-6 py-4 bg-gray-50">
                        <div class="flex flex-wrap items-center gap-4 text-sm">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                                <span class="text-gray-700">Lokasi Tersedia</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-gray-700">Lokasi Asal</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-red-500 rounded-full mr-2"></div>
                                <span class="text-gray-700">Lokasi Tujuan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Lokasi -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-list text-purple-600 mr-2"></i>
                Lokasi Tersedia
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($lokasi as $item)
                    <div
                        class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3 flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-blue-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 truncate">{{ $item->nama }}</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ $item->alamat }}</p>
                                <div class="mt-2 text-xs text-gray-500">
                                    <div>Lat: {{ $item->latitude }}</div>
                                    <div>Lng: {{ $item->longitude }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize map
                const map = L.map('map').setView([-6.921389, 107.607222], 12);

                // Add tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Lokasi data
                const lokasi = @json($lokasi);
                let markers = {};

                // Add markers for all locations
                lokasi.forEach(function(loc) {
                    const marker = L.marker([loc.latitude, loc.longitude], {
                        icon: L.divIcon({
                            className: 'custom-marker',
                            html: '<div style="background-color: #3B82F6; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                            iconSize: [20, 20],
                            iconAnchor: [10, 10]
                        })
                    }).addTo(map);

                    marker.bindPopup(`
            <div class="text-center">
                <h4 class="font-semibold">${loc.nama}</h4>
                <p class="text-sm text-gray-600">${loc.alamat}</p>
            </div>
        `);

                    markers[loc.id] = marker;
                });

                // Handle location selection
                document.getElementById('lokasi_asal').addEventListener('change', updateMarkers);
                document.getElementById('lokasi_tujuan').addEventListener('change', updateMarkers);

                function updateMarkers() {
                    const asalId = document.getElementById('lokasi_asal').value;
                    const tujuanId = document.getElementById('lokasi_tujuan').value;

                    // Reset all markers to default color
                    lokasi.forEach(function(loc) {
                        if (markers[loc.id]) {
                            map.removeLayer(markers[loc.id]);

                            let color = '#3B82F6'; // Default blue
                            if (loc.id == asalId) {
                                color = '#10B981'; // Green for origin
                            } else if (loc.id == tujuanId) {
                                color = '#EF4444'; // Red for destination
                            }

                            const marker = L.marker([loc.latitude, loc.longitude], {
                                icon: L.divIcon({
                                    className: 'custom-marker',
                                    html: `<div style="background-color: ${color}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
                                    iconSize: [20, 20],
                                    iconAnchor: [10, 10]
                                })
                            }).addTo(map);

                            marker.bindPopup(`
                    <div class="text-center">
                        <h4 class="font-semibold">${loc.nama}</h4>
                        <p class="text-sm text-gray-600">${loc.alamat}</p>
                        ${loc.id == asalId ? '<span class="text-green-600 font-medium">Lokasi Asal</span>' : ''}
                        ${loc.id == tujuanId ? '<span class="text-red-600 font-medium">Lokasi Tujuan</span>' : ''}
                    </div>
                `);

                            markers[loc.id] = marker;
                        }
                    });

                    // Center map if both locations selected
                    if (asalId && tujuanId) {
                        const asalLoc = lokasi.find(l => l.id == asalId);
                        const tujuanLoc = lokasi.find(l => l.id == tujuanId);

                        if (asalLoc && tujuanLoc) {
                            const group = new L.featureGroup([markers[asalId], markers[tujuanId]]);
                            map.fitBounds(group.getBounds().pad(0.1));
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection
