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
                        Hasil pencarian dengan instruksi turn-by-turn melalui {{ count($hasil['jalur']) }} titik
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

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Turn-by-Turn Directions Panel -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-md p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-route text-blue-600 mr-2"></i>
                        Panduan Arah
                    </h3>

                    <!-- Summary Stats -->
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <div class="text-lg font-bold text-blue-600">{{ $hasil['total_jarak'] }} km</div>
                            <div class="text-xs text-gray-600">Total Jarak</div>
                        </div>
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <div class="text-lg font-bold text-green-600">{{ $hasil['total_waktu'] }} min</div>
                            <div class="text-xs text-gray-600">Estimasi Waktu</div>
                        </div>
                    </div>

                    <!-- Turn-by-Turn Instructions -->
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @php
                            $directions = $hasil['directions'] ?? [];
                            if (empty($directions)) {
                                // Generate simple directions jika belum ada
                                $directions = [];
                                foreach ($hasil['jalur'] as $index => $lokasiId) {
                                    $lokasi = $detailLokasi[$lokasiId];
                                    if ($index === 0) {
                                        $directions[] = [
                                            'step' => 1,
                                            'instruction' => "🚀 Mulai perjalanan dari {$lokasi->nama}",
                                            'type' => 'start',
                                            'icon' => 'play',
                                        ];
                                    } elseif ($index === count($hasil['jalur']) - 1) {
                                        $directions[] = [
                                            'step' => $index + 1,
                                            'instruction' => "🏁 Tiba di {$lokasi->nama}",
                                            'type' => 'arrive',
                                            'icon' => 'flag',
                                        ];
                                    } else {
                                        $rute = \App\Models\Rute::where('lokasi_asal_id', $hasil['jalur'][$index - 1])
                                            ->where('lokasi_tujuan_id', $lokasiId)
                                            ->first();
                                        $directions[] = [
                                            'step' => $index + 1,
                                            'instruction' => "➡️ Lanjutkan ke {$lokasi->nama}",
                                            'distance_text' => $rute ? $rute->jarak . ' km' : '',
                                            'estimated_time' => $rute ? $rute->waktu_tempuh : 0,
                                            'type' => 'navigate',
                                            'icon' => 'arrow-right',
                                        ];
                                    }
                                }
                            }
                        @endphp

                        @foreach ($directions as $direction)
                            <div class="flex items-start p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors cursor-pointer"
                                onclick="focusOnStep({{ $direction['step'] ?? 1 }})">
                                <!-- Step Number & Icon -->
                                <div class="flex-shrink-0 mr-3">
                                    <div
                                        class="w-8 h-8 rounded-full flex items-center justify-center
                                        {{ ($direction['type'] ?? '') === 'start'
                                            ? 'bg-green-500 text-white'
                                            : (($direction['type'] ?? '') === 'arrive'
                                                ? 'bg-red-500 text-white'
                                                : 'bg-blue-500 text-white') }}">
                                        @if (($direction['type'] ?? '') === 'start')
                                            <i class="fas fa-play text-xs"></i>
                                        @elseif(($direction['type'] ?? '') === 'arrive')
                                            <i class="fas fa-flag text-xs"></i>
                                        @else
                                            <span class="text-xs font-bold">{{ $direction['step'] ?? 1 }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Instruction Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-gray-900 mb-1">
                                        {{ $direction['instruction'] ?? 'Instruksi tidak tersedia' }}
                                    </div>

                                    @if (isset($direction['landmark']) && $direction['landmark'])
                                        <div class="text-xs text-gray-600 mb-1">
                                            📍 {{ $direction['landmark'] }}
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        @if (isset($direction['distance_text']) && $direction['distance_text'])
                                            <span>
                                                <i class="fas fa-road mr-1"></i>
                                                {{ $direction['distance_text'] }}
                                            </span>
                                        @endif
                                        @if (isset($direction['estimated_time']) && $direction['estimated_time'] > 0)
                                            <span>
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $direction['estimated_time'] }} min
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Export & Share Buttons -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-2">
                            <button onclick="exportDirections()"
                                class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm hover:bg-blue-200 transition-colors">
                                <i class="fas fa-download mr-1"></i>
                                Export
                            </button>
                            <button onclick="shareDirections()"
                                class="px-3 py-2 bg-green-100 text-green-700 rounded-lg text-sm hover:bg-green-200 transition-colors">
                                <i class="fas fa-share mr-1"></i>
                                Bagikan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map & Details -->
            <div class="lg:col-span-3">
                <div class="space-y-6">
                    <!-- Interactive Map -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <i class="fas fa-map text-blue-600 mr-2"></i>
                                    Peta Rute dengan Instruksi
                                </h3>
                                <div class="flex items-center space-x-2 text-sm text-gray-600">
                                    <span class="flex items-center">
                                        <i class="fas fa-route mr-1"></i>
                                        {{ count($hasil['jalur']) }} titik
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-road mr-1"></i>
                                        {{ $hasil['total_jarak'] }} km
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div id="map" class="h-96"></div>

                        <!-- Map Legend -->
                        <div class="px-6 py-4 bg-gray-50">
                            <div class="flex flex-wrap items-center gap-4 text-sm">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                                    <span class="text-gray-700">Start</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-red-500 rounded-full mr-2"></div>
                                    <span class="text-gray-700">Finish</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                                    <span class="text-gray-700">Waypoint</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-2 bg-blue-600 mr-2"></div>
                                    <span class="text-gray-700">Route</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Route Details & Tips -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Route Segments -->
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-list text-purple-600 mr-2"></i>
                                Segmen Perjalanan
                            </h3>

                            <div class="space-y-3">
                                @foreach ($hasil['jalur'] as $index => $lokasiId)
                                    @if ($index < count($hasil['jalur']) - 1)
                                        @php
                                            $lokasi = $detailLokasi[$lokasiId];
                                            $nextLokasi = $detailLokasi[$hasil['jalur'][$index + 1]];
                                            $rute = \App\Models\Rute::where('lokasi_asal_id', $lokasiId)
                                                ->where('lokasi_tujuan_id', $hasil['jalur'][$index + 1])
                                                ->first();
                                        @endphp
                                        <div
                                            class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-3 text-xs font-bold">
                                                    {{ $index + 1 }}
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900 text-sm">
                                                        {{ $lokasi->nama }} → {{ $nextLokasi->nama }}
                                                    </div>
                                                    <div class="text-xs text-gray-600">
                                                        Segmen {{ $index + 1 }} dari {{ count($hasil['jalur']) - 1 }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                @if ($rute)
                                                    <div class="text-sm font-medium text-gray-900">{{ $rute->jarak }} km
                                                    </div>
                                                    <div class="text-xs text-gray-600">{{ $rute->waktu_tempuh }} min</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Travel Tips -->
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-lightbulb text-yellow-600 mr-2"></i>
                                Tips Perjalanan
                            </h3>

                            <div class="space-y-3">
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-start">
                                        <i class="fas fa-clock text-blue-600 mr-2 mt-1"></i>
                                        <div>
                                            <div class="font-medium text-blue-900">Estimasi Waktu</div>
                                            <div class="text-sm text-blue-800">
                                                Perjalanan sekitar {{ $hasil['total_waktu'] }} menit dengan jalan kaki.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3 bg-green-50 rounded-lg">
                                    <div class="flex items-start">
                                        <i class="fas fa-map-marked-alt text-green-600 mr-2 mt-1"></i>
                                        <div>
                                            <div class="font-medium text-green-900">Landmark Penting</div>
                                            <div class="text-sm text-green-800">
                                                Perhatikan {{ count($hasil['jalur']) - 2 }} titik transit untuk navigasi.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex items-start">
                                        <i class="fas fa-mobile-alt text-yellow-600 mr-2 mt-1"></i>
                                        <div>
                                            <div class="font-medium text-yellow-900">Tips Navigation</div>
                                            <div class="text-sm text-yellow-800">
                                                Screenshot instruksi ini untuk navigasi offline.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let map;
            let routeMarkers = [];

            document.addEventListener('DOMContentLoaded', function() {
                initializeMap();
            });

            function initializeMap() {
                const hasil = @json($hasil);
                const detailLokasi = @json($detailLokasi);

                // Initialize map
                map = L.map('map').setView([-6.903611, 107.588611], 16);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Create coordinates and markers
                const coordinates = [];

                hasil.jalur.forEach(function(lokasiId, index) {
                    const lokasi = detailLokasi[lokasiId];
                    if (!lokasi) return;

                    const lat = parseFloat(lokasi.latitude);
                    const lng = parseFloat(lokasi.longitude);
                    coordinates.push([lat, lng]);

                    // Create marker
                    let color, iconHtml, size;

                    if (index === 0) {
                        color = '#10B981';
                        iconHtml = '<i class="fas fa-play" style="color: white; font-size: 10px;"></i>';
                        size = 32;
                    } else if (index === hasil.jalur.length - 1) {
                        color = '#EF4444';
                        iconHtml = '<i class="fas fa-flag" style="color: white; font-size: 10px;"></i>';
                        size = 32;
                    } else {
                        color = '#3B82F6';
                        iconHtml = `<span style="color: white; font-size: 11px; font-weight: bold;">${index}</span>`;
                        size = 28;
                    }

                    const marker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'route-marker',
                            html: `<div style="
                                background: ${color};
                                width: ${size}px;
                                height: ${size}px;
                                border-radius: 50%;
                                border: 3px solid white;
                                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                ${iconHtml}
                            </div>`,
                            iconSize: [size, size],
                            iconAnchor: [size / 2, size / 2]
                        })
                    }).addTo(map);

                    // Popup content
                    let statusText = index === 0 ? 'Titik Start' :
                        index === hasil.jalur.length - 1 ? 'Tujuan Akhir' :
                        `Waypoint ${index}`;

                    marker.bindPopup(`
                        <div class="text-center p-3">
                            <h4 class="font-semibold text-gray-900 mb-1">${lokasi.nama}</h4>
                            <p class="text-sm text-gray-600 mb-2">${lokasi.alamat}</p>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium" style="background: ${color}20; color: ${color};">
                                ${statusText}
                            </span>
                        </div>
                    `);

                    routeMarkers.push(marker);
                });

                // Draw route line
                if (coordinates.length >= 2) {
                    L.polyline(coordinates, {
                        color: '#3B82F6',
                        weight: 6,
                        opacity: 0.8,
                        smoothFactor: 2
                    }).addTo(map);

                    // Fit map to route
                    const group = new L.featureGroup(routeMarkers);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            }

            function focusOnStep(stepNumber) {
                const markerIndex = stepNumber - 1;
                if (routeMarkers[markerIndex]) {
                    map.setView(routeMarkers[markerIndex].getLatLng(), 17);
                    routeMarkers[markerIndex].openPopup();
                }
            }

            function exportDirections() {
                const hasil = @json($hasil);
                let exportText = "PANDUAN RUTE TERPENDEK\n";
                exportText += "========================\n\n";
                exportText += `Jarak Total: ${hasil.total_jarak} km\n`;
                exportText += `Waktu Tempuh: ${hasil.total_waktu} menit\n`;
                exportText += `Tanggal: ${new Date().toLocaleDateString('id-ID')}\n\n`;
                exportText += "INSTRUKSI:\n";
                exportText += "----------\n\n";

                @foreach ($hasil['jalur'] as $index => $lokasiId)
                    @php $lokasi = $detailLokasi[$lokasiId]; @endphp
                    exportText += "{{ $index + 1 }}. ";
                    @if ($index === 0)
                        exportText += "🚀 Mulai dari {{ $lokasi->nama }}\n";
                    @elseif ($index === count($hasil['jalur']) - 1)
                        exportText += "🏁 Tiba di {{ $lokasi->nama }}\n";
                    @else
                        exportText += "➡️ Lanjutkan ke {{ $lokasi->nama }}\n";
                    @endif
                    exportText += "   📍 {{ $lokasi->alamat }}\n\n";
                @endforeach

                exportText += "========================\n";
                exportText += "Generated by Sistem Rute Terpendek";

                const element = document.createElement('a');
                const file = new Blob([exportText], {
                    type: 'text/plain'
                });
                element.href = URL.createObjectURL(file);
                element.download = `rute_${new Date().getTime()}.txt`;
                document.body.appendChild(element);
                element.click();
                document.body.removeChild(element);

                showNotification('Instruksi rute berhasil diexport!', 'success');
            }

            function shareDirections() {
                const shareText = `🗺️ RUTE TERPENDEK\n\n` +
                    `📏 Jarak: {{ $hasil['total_jarak'] }} km\n` +
                    `⏱️ Waktu: {{ $hasil['total_waktu'] }} menit\n` +
                    `🔄 Melalui: {{ count($hasil['jalur']) }} lokasi\n\n` +
                    `Generated by Sistem Rute Terpendek`;

                if (navigator.share) {
                    navigator.share({
                        title: 'Rute Terpendek',
                        text: shareText,
                        url: window.location.href
                    });
                } else {
                    navigator.clipboard.writeText(shareText + '\n\n' + window.location.href)
                        .then(() => showNotification('Link disalin ke clipboard!', 'success'))
                        .catch(() => showNotification('Gagal menyalin', 'error'));
                }
            }

            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg text-white z-50 ${
                    type === 'success' ? 'bg-green-500' : 
                    type === 'error' ? 'bg-red-500' : 'bg-blue-500'
                }`;
                notification.textContent = message;
                document.body.appendChild(notification);

                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 3000);
            }
        </script>
    @endpush
@endsection
