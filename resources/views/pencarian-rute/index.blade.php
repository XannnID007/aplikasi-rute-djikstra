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
                    Temukan rute tercepat di Kelurahan Pasirkaliki menggunakan Algoritma Dijkstra
                </p>
                <div class="mt-4 inline-flex items-center px-4 py-2 bg-blue-800 rounded-full text-sm">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Wilayah: Kelurahan Pasirkaliki, Cicendo, Bandung
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Pencarian -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-md p-6 sticky top-20">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-search text-blue-600 mr-2"></i>
                        Cari Rute Multi-Point
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
                                        {{ old('lokasi_asal') == $item->id ? 'selected' : '' }}
                                        data-lat="{{ $item->latitude }}" data-lng="{{ $item->longitude }}">
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
                                        {{ old('lokasi_tujuan') == $item->id ? 'selected' : '' }}
                                        data-lat="{{ $item->latitude }}" data-lng="{{ $item->longitude }}">
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
                            Tentang Sistem Ini
                        </h4>
                        <div class="text-sm text-blue-800 space-y-2">
                            <p>
                                <strong>Algoritma Dijkstra:</strong> Mencari jalur terpendek dengan menganalisis semua
                                kemungkinan rute melalui titik-titik transit.
                            </p>
                            <p>
                                <strong>Multi-Point Route:</strong> Rute tidak langsung, melainkan melalui beberapa titik
                                penting di kelurahan.
                            </p>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-lg font-bold text-gray-900">{{ $lokasi->count() }}</div>
                            <div class="text-xs text-gray-600">Lokasi</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-lg font-bold text-gray-900">
                                {{ \App\Models\Rute::count() }}
                            </div>
                            <div class="text-xs text-gray-600">Rute</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Peta dan Informasi -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <!-- Header Peta -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-map text-green-600 mr-2"></i>
                                Peta Kelurahan Pasirkaliki
                            </h3>
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-eye mr-1"></i>
                                Zoom: Tingkat Kelurahan
                            </div>
                        </div>
                    </div>

                    <!-- Peta -->
                    <div id="map" class="h-96"></div>

                    <!-- Controls -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <!-- Legenda -->
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

                            <!-- Map Controls -->
                            <div class="flex items-center space-x-2">
                                <button onclick="resetMapView()"
                                    class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm hover:bg-blue-200 transition-colors">
                                    <i class="fas fa-home mr-1"></i>Reset View
                                </button>
                                <button onclick="showAllRoutes()"
                                    class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm hover:bg-green-200 transition-colors">
                                    <i class="fas fa-route mr-1"></i>Lihat Rute
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Lokasi -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-list text-purple-600 mr-2"></i>
                    Lokasi di Kelurahan Pasirkaliki
                </h3>
                <div class="text-sm text-gray-600">
                    Total: {{ $lokasi->count() }} lokasi
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($lokasi as $item)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition-all duration-200 cursor-pointer"
                        onclick="focusLocation({{ $item->latitude }}, {{ $item->longitude }}, '{{ $item->nama }}')">
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3 flex-shrink-0">
                                @if (str_contains($item->nama, 'Masjid') || str_contains($item->nama, 'Musholla'))
                                    <i class="fas fa-mosque text-blue-600"></i>
                                @elseif(str_contains($item->nama, 'Sekolah') || str_contains($item->nama, 'SDN'))
                                    <i class="fas fa-school text-blue-600"></i>
                                @elseif(str_contains($item->nama, 'Puskesmas'))
                                    <i class="fas fa-hospital text-blue-600"></i>
                                @elseif(str_contains($item->nama, 'Pasar'))
                                    <i class="fas fa-store text-blue-600"></i>
                                @elseif(str_contains($item->nama, 'Kantor'))
                                    <i class="fas fa-building text-blue-600"></i>
                                @elseif(str_contains($item->nama, 'Taman') || str_contains($item->nama, 'Lapangan'))
                                    <i class="fas fa-tree text-blue-600"></i>
                                @elseif(str_contains($item->nama, 'Pos'))
                                    <i class="fas fa-shield-alt text-blue-600"></i>
                                @else
                                    <i class="fas fa-map-marker-alt text-blue-600"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 truncate">{{ $item->nama }}</h4>
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $item->alamat }}</p>
                                <div class="mt-2 flex items-center text-xs text-gray-500">
                                    <i class="fas fa-map-pin mr-1"></i>
                                    <span>{{ number_format($item->latitude, 4) }},
                                        {{ number_format($item->longitude, 4) }}</span>
                                </div>

                                <!-- Quick Action Buttons -->
                                <div class="mt-3 flex space-x-2">
                                    <button
                                        onclick="event.stopPropagation(); setAsOrigin({{ $item->id }}, '{{ $item->nama }}')"
                                        class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs hover:bg-green-200 transition-colors">
                                        <i class="fas fa-play mr-1"></i>Asal
                                    </button>
                                    <button
                                        onclick="event.stopPropagation(); setAsDestination({{ $item->id }}, '{{ $item->nama }}')"
                                        class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs hover:bg-red-200 transition-colors">
                                        <i class="fas fa-flag mr-1"></i>Tujuan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Info Panel -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <div class="bg-blue-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-route text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Multi-Point Routing</h3>
                <p class="text-gray-600 text-sm">Sistem mencari rute melalui beberapa titik transit untuk hasil yang
                    optimal</p>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <div class="bg-green-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-map-marked-alt text-2xl text-green-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Akurasi Tinggi</h3>
                <p class="text-gray-600 text-sm">Data koordinat dan jarak telah diverifikasi untuk wilayah Pasirkaliki</p>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <div class="bg-purple-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-clock text-2xl text-purple-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Estimasi Real-time</h3>
                <p class="text-gray-600 text-sm">Perhitungan waktu tempuh berdasarkan kondisi jalan lokal</p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let map;
            let markers = {};
            let routeLines = [];

            document.addEventListener('DOMContentLoaded', function() {
                // Initialize map dengan fokus pada Kelurahan Pasirkaliki
                map = L.map('map').setView([-6.903611, 107.588611], 17);

                // Add tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Lokasi data
                const lokasi = @json($lokasi);

                // Add markers for all locations
                lokasi.forEach(function(loc) {
                    const marker = L.marker([loc.latitude, loc.longitude], {
                        icon: L.divIcon({
                            className: 'custom-marker',
                            html: `<div style="
                                background-color: #3B82F6; 
                                width: 24px; 
                                height: 24px; 
                                border-radius: 50%; 
                                border: 2px solid white; 
                                box-shadow: 0 2px 4px rgba(0,0,0,0.3);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: white;
                                font-size: 10px;
                                font-weight: bold;
                            ">
                                <i class="fas fa-circle" style="font-size: 6px;"></i>
                            </div>`,
                            iconSize: [24, 24],
                            iconAnchor: [12, 12]
                        })
                    }).addTo(map);

                    marker.bindPopup(`
                        <div class="text-center p-2">
                            <h4 class="font-semibold text-gray-900 mb-1">${loc.nama}</h4>
                            <p class="text-sm text-gray-600 mb-2">${loc.alamat}</p>
                            <div class="flex space-x-2 mt-2">
                                <button onclick="setAsOrigin(${loc.id}, '${loc.nama}')" 
                                    class="px-2 py-1 bg-green-500 text-white rounded text-xs">
                                    <i class="fas fa-play mr-1"></i>Asal
                                </button>
                                <button onclick="setAsDestination(${loc.id}, '${loc.nama}')" 
                                    class="px-2 py-1 bg-red-500 text-white rounded text-xs">
                                    <i class="fas fa-flag mr-1"></i>Tujuan
                                </button>
                            </div>
                        </div>
                    `);

                    markers[loc.id] = marker;
                });

                // Handle location selection
                document.getElementById('lokasi_asal').addEventListener('change', updateMarkers);
                document.getElementById('lokasi_tujuan').addEventListener('change', updateMarkers);
            });

            function updateMarkers() {
                const asalId = document.getElementById('lokasi_asal').value;
                const tujuanId = document.getElementById('lokasi_tujuan').value;
                const lokasi = @json($lokasi);

                // Reset all markers to default color
                lokasi.forEach(function(loc) {
                    if (markers[loc.id]) {
                        map.removeLayer(markers[loc.id]);

                        let color = '#3B82F6'; // Default blue
                        let icon = '<i class="fas fa-circle" style="font-size: 6px;"></i>';

                        if (loc.id == asalId) {
                            color = '#10B981'; // Green for origin
                            icon = '<i class="fas fa-play" style="font-size: 8px;"></i>';
                        } else if (loc.id == tujuanId) {
                            color = '#EF4444'; // Red for destination
                            icon = '<i class="fas fa-flag" style="font-size: 8px;"></i>';
                        }

                        const marker = L.marker([loc.latitude, loc.longitude], {
                            icon: L.divIcon({
                                className: 'custom-marker',
                                html: `<div style="
                                    background-color: ${color}; 
                                    width: 28px; 
                                    height: 28px; 
                                    border-radius: 50%; 
                                    border: 3px solid white; 
                                    box-shadow: 0 3px 6px rgba(0,0,0,0.4);
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    color: white;
                                    font-weight: bold;
                                ">
                                    ${icon}
                                </div>`,
                                iconSize: [28, 28],
                                iconAnchor: [14, 14]
                            })
                        }).addTo(map);

                        let statusText = '';
                        if (loc.id == asalId) statusText =
                            '<span class="text-green-600 font-medium">📍 Lokasi Asal</span>';
                        if (loc.id == tujuanId) statusText =
                            '<span class="text-red-600 font-medium">🏁 Lokasi Tujuan</span>';

                        marker.bindPopup(`
                            <div class="text-center p-2">
                                <h4 class="font-semibold text-gray-900 mb-1">${loc.nama}</h4>
                                <p class="text-sm text-gray-600 mb-2">${loc.alamat}</p>
                                ${statusText}
                                <div class="flex space-x-2 mt-2">
                                    <button onclick="setAsOrigin(${loc.id}, '${loc.nama}')" 
                                        class="px-2 py-1 bg-green-500 text-white rounded text-xs">
                                        <i class="fas fa-play mr-1"></i>Asal
                                    </button>
                                    <button onclick="setAsDestination(${loc.id}, '${loc.nama}')" 
                                        class="px-2 py-1 bg-red-500 text-white rounded text-xs">
                                        <i class="fas fa-flag mr-1"></i>Tujuan
                                    </button>
                                </div>
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

            function resetMapView() {
                map.setView([-6.903611, 107.588611], 17);
            }

            function focusLocation(lat, lng, nama) {
                map.setView([lat, lng], 18);

                // Find and open popup for this location
                Object.values(markers).forEach(marker => {
                    if (marker.getLatLng().lat === lat && marker.getLatLng().lng === lng) {
                        marker.openPopup();
                    }
                });
            }

            function setAsOrigin(id, nama) {
                document.getElementById('lokasi_asal').value = id;
                updateMarkers();

                // Show toast notification
                showNotification(`Lokasi asal: ${nama}`, 'success');
            }

            function setAsDestination(id, nama) {
                document.getElementById('lokasi_tujuan').value = id;
                updateMarkers();

                // Show toast notification
                showNotification(`Lokasi tujuan: ${nama}`, 'success');
            }

            function showAllRoutes() {
                // Clear existing route lines
                routeLines.forEach(line => map.removeLayer(line));
                routeLines = [];

                // Fetch and display some sample routes
                fetch('/api/sample-routes') // You might need to create this endpoint
                    .then(response => response.json())
                    .then(routes => {
                        routes.forEach(route => {
                            const line = L.polyline(route.coordinates, {
                                color: '#94A3B8',
                                weight: 2,
                                opacity: 0.6,
                                dashArray: '5, 5'
                            }).addTo(map);

                            routeLines.push(line);
                        });
                    })
                    .catch(() => {
                        showNotification('Tidak dapat memuat preview rute', 'error');
                    });
            }

            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 ${
                    type === 'success' ? 'bg-green-500' : 
                    type === 'error' ? 'bg-red-500' : 'bg-blue-500'
                }`;
                notification.textContent = message;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        </script>
    @endpush
@endsection
