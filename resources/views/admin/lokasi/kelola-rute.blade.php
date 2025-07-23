@extends('layouts.admin')

@section('title', 'Kelola Rute')
@section('page-title', 'Kelola Rute')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kelola Rute - {{ $lokasi->nama }}</h1>
                <p class="text-gray-600 mt-1">Atur rute dari lokasi ini ke lokasi lainnya</p>
            </div>
            <a href="{{ route('admin.lokasi.show', $lokasi) }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Tambah Rute -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-plus text-green-600 mr-2"></i>
                        Tambah Rute Baru
                    </h3>

                    <form action="{{ route('admin.lokasi.simpan-rute', $lokasi) }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Lokasi Asal (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi Asal
                            </label>
                            <input type="text" value="{{ $lokasi->nama }}"
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg" readonly>
                        </div>

                        <!-- Lokasi Tujuan -->
                        <div>
                            <label for="lokasi_tujuan_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi Tujuan <span class="text-red-500">*</span>
                            </label>
                            <select id="lokasi_tujuan_id" name="lokasi_tujuan_id"
                                class="w-full px-3 py-2 border @error('lokasi_tujuan_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                required>
                                <option value="">Pilih lokasi tujuan</option>
                                @foreach ($semuaLokasi as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('lokasi_tujuan_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('lokasi_tujuan_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jarak -->
                        <div>
                            <label for="jarak" class="block text-sm font-medium text-gray-700 mb-2">
                                Jarak (km) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="jarak" name="jarak" value="{{ old('jarak') }}" step="0.01"
                                min="0"
                                class="w-full px-3 py-2 border @error('jarak') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                placeholder="Contoh: 5.5" required>
                            @error('jarak')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Waktu Tempuh -->
                        <div>
                            <label for="waktu_tempuh" class="block text-sm font-medium text-gray-700 mb-2">
                                Waktu Tempuh (menit) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="waktu_tempuh" name="waktu_tempuh" value="{{ old('waktu_tempuh') }}"
                                min="0"
                                class="w-full px-3 py-2 border @error('waktu_tempuh') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                placeholder="Contoh: 25" required>
                            @error('waktu_tempuh')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Rute
                        </button>
                    </form>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 rounded-lg p-4 mt-6 border border-blue-200">
                    <h4 class="font-medium text-blue-900 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Petunjuk:
                    </h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Jarak diukur dalam kilometer</li>
                        <li>• Waktu tempuh dalam menit</li>
                        <li>• Rute akan digunakan dalam perhitungan algoritma Dijkstra</li>
                        <li>• Pastikan data akurat untuk hasil optimal</li>
                    </ul>
                </div>
            </div>

            <!-- Daftar Rute -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-list text-blue-600 mr-2"></i>
                            Daftar Rute dari {{ $lokasi->nama }}
                        </h3>
                    </div>

                    @if ($ruteAsal->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lokasi Tujuan</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jarak (km)</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Waktu (menit)</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($ruteAsal as $index => $rute)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                                        <i class="fas fa-map-marker-alt text-blue-600"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $rute->lokasiTujuan->nama }}</div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ Str::limit($rute->lokasiTujuan->alamat, 30) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm font-medium text-gray-900">{{ $rute->jarak }}</span>
                                                <span class="text-xs text-gray-500">km</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="text-sm font-medium text-gray-900">{{ $rute->waktu_tempuh }}</span>
                                                <span class="text-xs text-gray-500">menit</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <form action="{{ route('admin.rute.hapus', $rute) }}" method="POST"
                                                    class="inline"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus rute ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900 p-2 rounded"
                                                        title="Hapus Rute">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div
                                class="bg-gray-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                <i class="fas fa-route text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada rute</h3>
                            <p class="text-gray-600 mb-4">Tambahkan rute pertama dari lokasi ini</p>
                        </div>
                    @endif
                </div>

                <!-- Statistik -->
                <div class="bg-white rounded-xl shadow-md p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-chart-line text-purple-600 mr-2"></i>
                        Statistik Rute
                    </h3>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $ruteAsal->count() }}</div>
                            <div class="text-sm text-gray-600">Total Rute</div>
                        </div>

                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">
                                {{ $ruteAsal->count() > 0 ? number_format($ruteAsal->avg('jarak'), 1) : 0 }}
                            </div>
                            <div class="text-sm text-gray-600">Rata-rata Jarak (km)</div>
                        </div>

                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ $ruteAsal->count() > 0 ? round($ruteAsal->avg('waktu_tempuh')) : 0 }}
                            </div>
                            <div class="text-sm text-gray-600">Rata-rata Waktu (menit)</div>
                        </div>

                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">
                                {{ $ruteAsal->count() > 0 ? $ruteAsal->max('jarak') : 0 }}
                            </div>
                            <div class="text-sm text-gray-600">Jarak Terjauh (km)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Auto calculate time based on distance (optional helper)
            document.getElementById('jarak').addEventListener('input', function() {
                const jarak = parseFloat(this.value);
                const waktuInput = document.getElementById('waktu_tempuh');

                if (!isNaN(jarak) && jarak > 0) {
                    // Estimate: 40 km/h average speed in city
                    const estimatedTime = Math.round((jarak / 40) * 60);

                    if (!waktuInput.value) {
                        waktuInput.value = estimatedTime;
                        waktuInput.style.backgroundColor = '#FEF3C7'; // Yellow background

                        setTimeout(() => {
                            waktuInput.style.backgroundColor = '';
                        }, 2000);
                    }
                }
            });
        </script>
    @endpush
@endsection
