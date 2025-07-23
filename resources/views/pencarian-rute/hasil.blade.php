@extends('layouts.user')

@section('title', 'Hasil Pencarian Rute')

@section('content')
    <div class="space-y-6">
        <!-- Header Results -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2">
                        <i class="fas fa-check-circle mr-2"></i>
                        Rute Terpendek Ditemukan!
                    </h1>
                    <p class="text-green-100">
                        Hasil pencarian menggunakan Algoritma Dijkstra
                    </p>
                </div>
                <div class="text-right">
                    <a href="{{ route('pencarian-rute.index') }}"
                        class="bg-white text-green-600 hover:bg-gray-100 px-4 py-2 rounded-lg transition-colors duration-200 inline-flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Cari Lagi
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informasi Rute -->
            <div class="lg:col-span-1">
                <div class="space-y-6">
                    <!-- Ringkasan Rute -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Ringkasan Rute
                        </h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-600">Total Jarak</span>
                                <span class="text-lg font-bold text-blue-600">{{ $hasil['total_jarak'] }} km</span>
                            </div>

                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-600">Estimasi Waktu</span>
                                <span class="text-lg font-bold text-green-600">{{ $hasil['total_waktu'] }} menit</span>
                            </div>

                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-600">Jumlah Titik</span>
                                <span class="text-lg font-bold text-purple-600">{{ count($hasil['jalur']) }} lokasi</span>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Jalur -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-route text-green-600 mr-2"></i>
                            Detail Jalur
                        </h3>

                        <div class="space-y-3">
                            @foreach ($hasil['jalur'] as $index => $lokasiId)
                                @php $lokasi = $detailLokasi[$lokasiId]; @endphp
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-3">
                                        @if ($index === 0)
                                            <!-- Titik Asal -->
                                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-play text-white text-xs"></i>
                                            </div>
                                        @elseif($index === count($hasil['jalur']) - 1)
                                            <!-- Titik Tujuan -->
                                            <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-flag text-white text-xs"></i>
                                            </div>
                                        @else
                                            <!-- Titik Transit -->
                                            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                                <span class="text-white text-xs font-bold">{{ $index }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $lokasi->nama }}</h4>
                                        <p class="text-sm text-gray-600">{{ $lokasi->alamat }}</p>
                                    </div>

                                    @if ($index < count($hasil['jalur']) - 1)
                                        <div class="flex-shrink-0 ml-2">
                                            <i class="fas fa-arrow-down text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>

                                @if ($index < count($hasil['jalur']) - 1)
                                    <div class="ml-3 pl-3 border-l-2 border-gray-200">
                                        <div class="text-xs text-gray-500 py-1">
                                            @php
                                                $rute = \App\Models\Rute::where('lokasi_asal_id', $lokasiId)
                                                    ->where('lokasi_tujuan_id', $hasil['jalur'][$index + 1])
                                                    ->first();
                                            @endphp
                                            @if ($rute)
                                                {{ $rute->jarak }} km • {{ $rute->waktu_tempuh }} menit
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

            <!-- Peta Hasil -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <!-- Header Peta -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-map text-blue-600 mr-2"></i>
                            Visualisasi Rute
                        </h3>
                    </div>

                    <!-- Peta -->
                    <div id="map" class="h-96"></div>

                    <!-- Legenda -->
                    <div class="px-6 py-4 bg-gray-50">
                        <div class="flex flex-wrap items-center gap-4 text-sm">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-gray-700">Lokasi Asal</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-red-500 rounded-full mr-2"></div>
                                <span class="text-gray-700">Lokasi Tujuan</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                                <span class="text-gray-700">Titik Transit</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-2 bg-blue-600 mr-2"></div>
                                <span class="text-gray-700">Rute Terpendek</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Data hasil pencarian
                const hasil = @json($hasil);
                const detailLokasi = @json($detailLokasi);

                // Initialize map
                const map = L.map('map');

                // Add tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Prepare coordinates for route
                const coordinates = [];
                const markers = [];

                hasil.jalur.forEach(function(lokasiId, index) {
                    const lokasi = detailLokasi[lokasiId];
                    coordinates.push([lokasi.latitude, lokasi.longitude]);

                    // Determine marker color and icon
                    let color, icon;
                    if (index === 0) {
                        color = '#10B981'; // Green for start
                        icon = 'play';
                    } else if (index === hasil.jalur.length - 1) {
                        color = '#EF4444'; // Red for end
                        icon = 'flag';
                    } else {
                        color = '#3B82F6'; // Blue for waypoints
                        icon = index.toString();
                    }

                    // Create marker
                    const marker = L.marker([lokasi.latitude, lokasi.longitude], {
                        icon: L.divIcon({
                            className: 'custom-marker',
                            html: `<div style="background-color: ${color}; width: 24px; height: 24px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-size: 10px; font-weight: bold;">
                    ${icon === 'play' ? '<i class="fas fa-play" style="font-size: 8px;"></i>' : 
                      icon === 'flag' ? '<i class="fas fa-flag" style="font-size: 8px;"></i>' : 
                      icon}
                </div>`,
                            iconSize: [24, 24],
                            iconAnchor: [12, 12]
                        })
                    }).addTo(map);

                    // Add popup
                    marker.bindPopup(`
            <div class="text-center">
                <h4 class="font-semibold">${lokasi.nama}</h4>
                <p class="text-sm text-gray-600">${lokasi.alamat}</p>
                <span class="text-xs font-medium ${
                    index === 0 ? 'text-green-600' : 
                    index === hasil.jalur.length - 1 ? 'text-red-600' : 
                    'text-blue-600'
                }">
                    ${index === 0 ? 'Lokasi Asal' : 
                      index === hasil.jalur.length - 1 ? 'Lokasi Tujuan' : 
                      'Titik Transit ' + index}
                </span>
            </div>
        `);

                    markers.push(marker);
                });

                // Draw route line
                const routeLine = L.polyline(coordinates, {
                    color: '#3B82F6',
                    weight: 4,
                    opacity: 0.8,
                    smoothFactor: 1
                }).addTo(map);

                // Fit map to show all markers
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            });

            function printRoute() {
                window.print();
            }

            function shareRoute() {
                if (navigator.share) {
                    navigator.share({
                        title: 'Rute Terpendek',
                        text: `Rute terpendek dengan jarak ${@json($hasil['total_jarak'])} km dan waktu tempuh ${@json($hasil['total_waktu'])} menit`,
                        url: window.location.href
                    });
                } else {
                    // Fallback: copy to clipboard
                    navigator.clipboard.writeText(window.location.href).then(function() {
                        alert('Link rute telah disalin ke clipboard!');
                    });
                }
            }
        </script>
    @endpush
@endsection
