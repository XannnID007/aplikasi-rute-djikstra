@extends('layouts.user')

@section('title', 'Detail Riwayat Pencarian')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2">
                        <i class="fas fa-eye mr-2"></i>
                        Detail Riwayat Pencarian
                    </h1>
                    <p class="text-blue-100">
                        Pencarian #{{ $pencarianRute->id }} - {{ $pencarianRute->created_at->format('d F Y, H:i') }}
                    </p>
                </div>
                <div class="text-right">
                    <a href="{{ route('pencarian-rute.riwayat') }}"
                        class="bg-white text-blue-600 hover:bg-gray-100 px-4 py-2 rounded-lg transition-colors duration-200 inline-flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Riwayat
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Detail Informasi -->
            <div class="lg:col-span-1">
                <div class="space-y-6">
                    <!-- Ringkasan Pencarian -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Ringkasan Pencarian
                        </h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-600">ID Pencarian</span>
                                <span class="text-lg font-bold text-gray-900">#{{ $pencarianRute->id }}</span>
                            </div>

                            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-600">Total Jarak</span>
                                <span class="text-lg font-bold text-blue-600">{{ $pencarianRute->total_jarak }} km</span>
                            </div>

                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-600">Estimasi Waktu</span>
                                <span class="text-lg font-bold text-green-600">{{ $pencarianRute->total_waktu }}
                                    menit</span>
                            </div>

                            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-600">Jumlah Titik</span>
                                <span class="text-lg font-bold text-purple-600">{{ count($pencarianRute->jalur_rute) }}
                                    lokasi</span>
                            </div>

                            <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-600">Waktu Pencarian</span>
                                <span
                                    class="text-sm font-bold text-orange-600">{{ $pencarianRute->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Jalur -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-route text-green-600 mr-2"></i>
                            Detail Jalur Perjalanan
                        </h3>

                        <div class="space-y-3">
                            @foreach ($pencarianRute->jalur_rute as $index => $lokasiId)
                                @php $lokasi = $detailLokasi[$lokasiId]; @endphp
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-3">
                                        @if ($index === 0)
                                            <!-- Titik Asal -->
                                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-play text-white text-sm"></i>
                                            </div>
                                        @elseif($index === count($pencarianRute->jalur_rute) - 1)
                                            <!-- Titik Tujuan -->
                                            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-flag text-white text-sm"></i>
                                            </div>
                                        @else
                                            <!-- Titik Transit -->
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-bold">{{ $index }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $lokasi->nama }}</h4>
                                        <p class="text-sm text-gray-600">{{ $lokasi->alamat }}</p>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Lat: {{ $lokasi->latitude }}, Lng: {{ $lokasi->longitude }}
                                        </div>

                                        @if ($index === 0)
                                            <span
                                                class="inline-block mt-1 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                                Lokasi Asal
                                            </span>
                                        @elseif($index === count($pencarianRute->jalur_rute) - 1)
                                            <span
                                                class="inline-block mt-1 px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">
                                                Lokasi Tujuan
                                            </span>
                                        @else
                                            <span
                                                class="inline-block mt-1 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                                Titik Transit {{ $index }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if ($index < count($pencarianRute->jalur_rute) - 1)
                                    <div class="ml-4 pl-4 border-l-2 border-gray-200">
                                        <div class="text-xs text-gray-500 py-2">
                                            @php
                                                $rute = \App\Models\Rute::where('lokasi_asal_id', $lokasiId)
                                                    ->where('lokasi_tujuan_id', $pencarianRute->jalur_rute[$index + 1])
                                                    ->first();
                                            @endphp
                                            @if ($rute)
                                                <div class="flex items-center space-x-4">
                                                    <span class="flex items-center">
                                                        <i class="fas fa-road mr-1 text-blue-500"></i>
                                                        {{ $rute->jarak }} km
                                                    </span>
                                                    <span class="flex items-center">
                                                        <i class="fas fa-clock mr-1 text-green-500"></i>
                                                        {{ $rute->waktu_tempuh }} menit
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Aksi -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-tools text-orange-600 mr-2"></i>
                            Aksi
                        </h3>

                        <div class="space-y-3">
                            <form action="{{ route('pencarian-rute.cari') }}" method="POST">
                                @csrf
                                <input type="hidden" name="lokasi_asal" value="{{ $pencarianRute->lokasi_asal_id }}">
                                <input type="hidden" name="lokasi_tujuan" value="{{ $pencarianRute->lokasi_tujuan_id }}">
                                <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                                    <i class="fas fa-redo mr-2"></i>
                                    Ulangi Pencarian
                                </button>
                            </form>

                            <button onclick="printRoute()"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-print mr-2"></i>
                                Cetak Detail
                            </button>

                            <button onclick="shareRoute()"
                                class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-share mr-2"></i>
                                Bagikan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Peta -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <!-- Header Peta -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-map text-blue-600 mr-2"></i>
                            Visualisasi Rute di Peta
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
                                <span class="text-gray-700">Jalur Rute</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="bg-white rounded-xl shadow-md p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-info text-indigo-600 mr-2"></i>
                        Informasi Tambahan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Algoritma Dijkstra</h4>
                            <p class="text-sm text-gray-600">
                                Rute ini dihitung menggunakan algoritma Dijkstra yang menjamin jalur terpendek berdasarkan
                                jarak.
                            </p>
                        </div>

                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Estimasi Waktu</h4>
                            <p class="text-sm text-gray-600">
                                Waktu tempuh dihitung berdasarkan kecepatan rata-rata dan kondisi lalu lintas normal.
                            </p>
                        </div>

                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Akurasi Data</h4>
                            <p class="text-sm text-gray-600">
                                Data jarak dan koordinat lokasi telah diverifikasi untuk memastikan akurasi hasil.
                            </p>
                        </div>

                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Saran Perjalanan</h4>
                            <p class="text-sm text-gray-600">
                                Selalu periksa kondisi jalan dan cuaca sebelum melakukan perjalanan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Data rute dari backend
                const jalurRute = @json($pencarianRute->jalur_rute);
                const detailLokasi = @json($detailLokasi);

                // Initialize map
                const map = L.map('map');

                // Add tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Prepare coordinates and markers
                const coordinates = [];
                const markers = [];

                jalurRute.forEach(function(lokasiId, index) {
                    const lokasi = detailLokasi[lokasiId];
                    coordinates.push([lokasi.latitude, lokasi.longitude]);

                    // Determine marker style
                    let color, icon;
                    if (index === 0) {
                        color = '#10B981'; // Green for start
                        icon = '<i class="fas fa-play" style="font-size: 10px;"></i>';
                    } else if (index === jalurRute.length - 1) {
                        color = '#EF4444'; // Red for end
                        icon = '<i class="fas fa-flag" style="font-size: 10px;"></i>';
                    } else {
                        color = '#3B82F6'; // Blue for waypoints
                        icon = index.toString();
                    }

                    // Create marker
                    const marker = L.marker([lokasi.latitude, lokasi.longitude], {
                        icon: L.divIcon({
                            className: 'custom-marker',
                            html: `<div style="background-color: ${color}; width: 28px; height: 28px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-size: 11px; font-weight: bold;">
                    ${icon}
                </div>`,
                            iconSize: [28, 28],
                            iconAnchor: [14, 14]
                        })
                    }).addTo(map);

                    // Add popup with detailed info
                    marker.bindPopup(`
            <div class="text-center p-2">
                <h4 class="font-semibold text-gray-900 mb-1">${lokasi.nama}</h4>
                <p class="text-sm text-gray-600 mb-2">${lokasi.alamat}</p>
                <div class="text-xs text-gray-500 space-y-1">
                    <div>Lat: ${lokasi.latitude}</div>
                    <div>Lng: ${lokasi.longitude}</div>
                </div>
                <span class="inline-block mt-2 px-2 py-1 rounded-full text-xs font-medium" style="background-color: ${color}20; color: ${color};">
                    ${index === 0 ? 'Lokasi Asal' : 
                      index === jalurRute.length - 1 ? 'Lokasi Tujuan' : 
                      'Titik Transit ' + index}
                </span>
            </div>
        `);

                    markers.push(marker);
                });

                // Draw route line with enhanced styling
                const routeLine = L.polyline(coordinates, {
                    color: '#3B82F6',
                    weight: 5,
                    opacity: 0.8,
                    smoothFactor: 1,
                    dashArray: '10, 5'
                }).addTo(map);

                // Add arrow decorations along the route
                const decorator = L.polylineDecorator(routeLine, {
                    patterns: [{
                        offset: '5%',
                        repeat: '10%',
                        symbol: L.Symbol.arrowHead({
                            pixelSize: 15,
                            polygon: false,
                            pathOptions: {
                                stroke: true,
                                weight: 2,
                                color: '#1E40AF'
                            }
                        })
                    }]
                });

                // Fit map to show all points with padding
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            });

            function printRoute() {
                window.print();
            }

            function shareRoute() {
                const url = window.location.href;
                const text =
                    `Lihat detail rute dari ${@json($pencarianRute->lokasiAsal->nama)} ke ${@json($pencarianRute->lokasiTujuan->nama)} dengan jarak ${@json($pencarianRute->total_jarak)} km`;

                if (navigator.share) {
                    navigator.share({
                        title: 'Detail Rute Terpendek',
                        text: text,
                        url: url
                    });
                } else {
                    // Fallback: copy to clipboard
                    navigator.clipboard.writeText(`${text}\n${url}`).then(function() {
                        alert('Link rute telah disalin ke clipboard!');
                    });
                }
            }
        </script>
    @endpush
@endsection
