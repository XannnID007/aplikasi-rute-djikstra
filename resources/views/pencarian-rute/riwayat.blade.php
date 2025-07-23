@extends('layouts.user')

@section('title', 'Riwayat Pencarian')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2">
                        <i class="fas fa-history mr-2"></i>
                        Riwayat Pencarian Rute
                    </h1>
                    <p class="text-purple-100">
                        Lihat semua riwayat pencarian rute yang pernah Anda lakukan
                    </p>
                </div>
                <div class="text-right">
                    <a href="{{ route('pencarian-rute.index') }}"
                        class="bg-white text-purple-600 hover:bg-gray-100 px-4 py-2 rounded-lg transition-colors duration-200 inline-flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Cari Rute Baru
                    </a>
                </div>
            </div>
        </div>

        @if ($riwayat->count() > 0)
            <!-- Riwayat Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach ($riwayat as $item)
                    <div
                        class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                        <!-- Header Card -->
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="bg-blue-600 p-2 rounded-lg mr-3">
                                        <i class="fas fa-route text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">Pencarian #{{ $item->id }}</h3>
                                        <p class="text-sm text-gray-600">{{ $item->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-500">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Content Card -->
                        <div class="p-6">
                            <!-- Rute Info -->
                            <div class="space-y-3 mb-4">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-600">Dari:</span>
                                        <p class="font-medium text-gray-900">{{ $item->lokasiAsal->nama }}</p>
                                    </div>
                                </div>

                                <div class="ml-6 border-l-2 border-gray-200 h-4"></div>

                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-600">Ke:</span>
                                        <p class="font-medium text-gray-900">{{ $item->lokasiTujuan->nama }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <div class="text-lg font-bold text-blue-600">{{ $item->total_jarak }} km</div>
                                    <div class="text-xs text-gray-600">Jarak</div>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <div class="text-lg font-bold text-green-600">{{ $item->total_waktu }} min</div>
                                    <div class="text-xs text-gray-600">Waktu</div>
                                </div>
                                <div class="text-center p-3 bg-purple-50 rounded-lg">
                                    <div class="text-lg font-bold text-purple-600">{{ count($item->jalur_rute) }}</div>
                                    <div class="text-xs text-gray-600">Titik</div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                <a href="{{ route('pencarian-rute.detail-riwayat', $item) }}"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg transition-colors duration-200 text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i>
                                    Lihat Detail
                                </a>
                                <button onclick="repeatSearch({{ $item->lokasi_asal_id }}, {{ $item->lokasi_tujuan_id }})"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded-lg transition-colors duration-200 text-sm font-medium">
                                    <i class="fas fa-redo mr-1"></i>
                                    Ulangi
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $riwayat->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <div class="bg-gray-100 p-6 rounded-full w-20 h-20 mx-auto mb-6 flex items-center justify-center">
                    <i class="fas fa-history text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Belum Ada Riwayat</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Anda belum pernah melakukan pencarian rute. Mulai cari rute pertama Anda sekarang!
                </p>
                <a href="{{ route('pencarian-rute.index') }}"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-colors duration-200 inline-flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Mulai Pencarian
                </a>
            </div>
        @endif
    </div>

    <!-- Form untuk repeat search (hidden) -->
    <form id="repeatSearchForm" action="{{ route('pencarian-rute.cari') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" id="repeatAsal" name="lokasi_asal">
        <input type="hidden" id="repeatTujuan" name="lokasi_tujuan">
    </form>

    <script>
        function repeatSearch(asalId, tujuanId) {
            document.getElementById('repeatAsal').value = asalId;
            document.getElementById('repeatTujuan').value = tujuanId;
            document.getElementById('repeatSearchForm').submit();
        }
    </script>
@endsection
