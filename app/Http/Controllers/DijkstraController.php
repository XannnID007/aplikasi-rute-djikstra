<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Models\Rute;
use App\Models\PencarianRute;
use Illuminate\Http\Request;

class DijkstraController extends Controller
{
    public function index()
    {
        $lokasi = Lokasi::where('aktif', true)->get();
        return view('pencarian-rute.index', compact('lokasi'));
    }

    public function cariRute(Request $request)
    {
        $request->validate([
            'lokasi_asal' => 'required|exists:lokasi,id',
            'lokasi_tujuan' => 'required|exists:lokasi,id|different:lokasi_asal',
        ]);

        $lokasiAsal = $request->lokasi_asal;
        $lokasiTujuan = $request->lokasi_tujuan;

        // Implementasi algoritma Dijkstra dengan database rute
        $hasil = $this->dijkstraFromDatabase($lokasiAsal, $lokasiTujuan);

        if ($hasil === null) {
            return back()->with('error', 'Tidak ada rute yang tersedia dari lokasi asal ke lokasi tujuan.');
        }

        // Generate turn-by-turn directions
        $directions = $this->generateTurnByTurnDirections($hasil['jalur']);
        $hasil['directions'] = $directions;

        // Simpan riwayat pencarian
        PencarianRute::create([
            'user_id' => auth()->id(),
            'lokasi_asal_id' => $lokasiAsal,
            'lokasi_tujuan_id' => $lokasiTujuan,
            'jalur_rute' => $hasil['jalur'],
            'total_jarak' => $hasil['total_jarak'],
            'total_waktu' => $hasil['total_waktu'],
        ]);

        // Ambil detail lokasi untuk ditampilkan
        $detailLokasi = Lokasi::whereIn('id', $hasil['jalur'])->get()->keyBy('id');

        return view('pencarian-rute.hasil', compact('hasil', 'detailLokasi'));
    }

    /**
     * Generate turn-by-turn directions dari jalur rute
     */
    private function generateTurnByTurnDirections($jalur)
    {
        $directions = [];
        $totalDistance = 0;

        for ($i = 0; $i < count($jalur) - 1; $i++) {
            $currentId = $jalur[$i];
            $nextId = $jalur[$i + 1];

            // Ambil rute dan lokasi
            $rute = Rute::where('lokasi_asal_id', $currentId)
                ->where('lokasi_tujuan_id', $nextId)
                ->with(['lokasiAsal', 'lokasiTujuan'])
                ->first();

            if (!$rute) continue;

            $currentLokasi = $rute->lokasiAsal;
            $nextLokasi = $rute->lokasiTujuan;

            // Generate instruksi untuk segmen ini
            $segmentDirections = $this->generateSegmentDirections($i, $rute, $totalDistance, $jalur);
            $directions = array_merge($directions, $segmentDirections);

            $totalDistance += $rute->jarak;
        }

        // Tambahkan instruksi tiba di tujuan
        if (!empty($jalur)) {
            $tujuanLokasi = Lokasi::find(end($jalur));
            $directions[] = [
                'step' => count($directions) + 1,
                'type' => 'arrive',
                'instruction' => "🏁 Tiba di {$tujuanLokasi->nama}",
                'distance' => number_format($totalDistance * 1000, 0),
                'distance_text' => number_format($totalDistance, 2) . ' km',
                'icon' => 'flag-checkered',
                'landmark' => $tujuanLokasi->alamat,
                'estimated_time' => 0
            ];
        }

        return $directions;
    }

    /**
     * Generate instruksi untuk satu segmen rute
     */
    private function generateSegmentDirections($segmentIndex, $rute, $currentDistance, $fullRoute)
    {
        $directions = [];
        $currentLokasi = $rute->lokasiAsal;
        $nextLokasi = $rute->lokasiTujuan;

        // Instruksi mulai (hanya untuk segmen pertama)
        if ($segmentIndex === 0) {
            $directions[] = [
                'step' => 1,
                'type' => 'start',
                'instruction' => "🚀 Mulai perjalanan dari {$currentLokasi->nama}",
                'distance' => '0',
                'distance_text' => '0 m',
                'icon' => 'play-circle',
                'landmark' => $currentLokasi->alamat,
                'estimated_time' => 0
            ];

            // Instruksi keluar dari lokasi awal
            $exitInstruction = $this->generateExitInstruction($currentLokasi);
            if ($exitInstruction) {
                $directions[] = $exitInstruction;
            }
        }

        // Hitung bearing dan arah
        $bearing = $this->calculateBearing(
            $currentLokasi->latitude,
            $currentLokasi->longitude,
            $nextLokasi->latitude,
            $nextLokasi->longitude
        );

        $direction = $this->bearingToDirection($bearing);
        $turnDirection = $this->getTurnDirection($segmentIndex, $fullRoute, $bearing);

        // Instruksi navigasi utama
        $mainInstruction = $this->generateMainInstruction(
            $segmentIndex,
            $rute,
            $direction,
            $turnDirection,
            $currentDistance
        );

        $directions[] = $mainInstruction;

        // Landmark di tengah jalan (jika jarak > 200m)
        if ($rute->jarak > 0.2) {
            $landmarkInstruction = $this->generateLandmarkInstruction($rute, $currentDistance);
            if ($landmarkInstruction) {
                $directions[] = $landmarkInstruction;
            }
        }

        return $directions;
    }

    private function generateExitInstruction($lokasi)
    {
        $exitInstructions = [
            'Masjid' => [
                'text' => '🕌 Keluar dari kompleks masjid menuju jalan utama',
                'icon' => 'door-open'
            ],
            'Pasar' => [
                'text' => '🏪 Keluar dari area pasar menuju Jl. Pasirkaliki',
                'icon' => 'door-open'
            ],
            'Puskesmas' => [
                'text' => '🏥 Keluar dari halaman puskesmas',
                'icon' => 'door-open'
            ],
            'Kantor' => [
                'text' => '🏢 Keluar dari kompleks kantor kelurahan',
                'icon' => 'door-open'
            ],
            'SDN' => [
                'text' => '🏫 Keluar dari area sekolah',
                'icon' => 'door-open'
            ],
            'Taman' => [
                'text' => '🌳 Keluar dari area taman',
                'icon' => 'door-open'
            ]
        ];

        foreach ($exitInstructions as $keyword => $instruction) {
            if (str_contains($lokasi->nama, $keyword)) {
                return [
                    'step' => 2,
                    'type' => 'exit',
                    'instruction' => $instruction['text'],
                    'distance' => '50',
                    'distance_text' => '50 m',
                    'icon' => $instruction['icon'],
                    'landmark' => 'Pintu keluar ' . strtolower($keyword),
                    'estimated_time' => 1
                ];
            }
        }

        return null;
    }

    private function generateMainInstruction($segmentIndex, $rute, $direction, $turnDirection, $currentDistance)
    {
        $currentLokasi = $rute->lokasiAsal;
        $nextLokasi = $rute->lokasiTujuan;
        $distance = $rute->jarak;

        // Tentukan jenis instruksi berdasarkan nama lokasi
        $instruction = '';
        $icon = 'arrow-right';

        // Deteksi jenis jalan
        if (str_contains($nextLokasi->alamat, 'Gang')) {
            $instruction = "🛤️ {$turnDirection} masuk ke {$nextLokasi->alamat}";
            $icon = $turnDirection === 'Belok kiri' ? 'turn-left' : 'turn-right';
        } else if (str_contains($nextLokasi->alamat, 'Raya')) {
            $instruction = "🛣️ Lanjutkan lurus di jalan raya menuju {$nextLokasi->nama}";
            $icon = 'arrow-up';
        } else {
            // Instruksi umum
            if ($distance < 0.15) {
                $instruction = "🚶 Jalan kaki singkat menuju {$nextLokasi->nama}";
                $icon = 'walking';
            } else if ($distance < 0.3) {
                $instruction = "➡️ {$turnDirection} menuju {$nextLokasi->nama}";
                $icon = $turnDirection === 'Belok kiri' ? 'turn-left' : 'turn-right';
            } else {
                $instruction = "🚗 Lanjutkan ke arah {$direction} menuju {$nextLokasi->nama}";
                $icon = $this->getDirectionIcon($direction);
            }
        }

        // Tambahkan informasi jarak
        $distanceText = $distance < 1 ?
            number_format($distance * 1000, 0) . ' m' :
            number_format($distance, 2) . ' km';

        return [
            'step' => $segmentIndex + 3, // Start from step 3 (after start and exit)
            'type' => 'navigate',
            'instruction' => $instruction,
            'distance' => number_format(($currentDistance + $distance) * 1000, 0),
            'distance_text' => $distanceText,
            'icon' => $icon,
            'landmark' => $this->generateLandmarkText($currentLokasi, $nextLokasi),
            'estimated_time' => $rute->waktu_tempuh
        ];
    }

    private function generateLandmarkInstruction($rute, $currentDistance)
    {
        $midDistance = $currentDistance + ($rute->jarak / 2);

        $landmarks = [
            '🏪 Lewati area pertokoan di kiri jalan',
            '🏘️ Lewati kompleks perumahan di kanan',
            '🚏 Lewati halte angkot',
            '🌳 Lewati pohon besar di sisi jalan',
            '⛽ Lewati warung kopi di kiri jalan',
            '📍 Lanjutkan lurus mengikuti jalan utama'
        ];

        return [
            'step' => 999, // Will be renumbered
            'type' => 'landmark',
            'instruction' => $landmarks[array_rand($landmarks)],
            'distance' => number_format($midDistance * 1000, 0),
            'distance_text' => number_format($rute->jarak / 2, 2) . ' km',
            'icon' => 'map-marker',
            'landmark' => 'Titik tengah perjalanan',
            'estimated_time' => round($rute->waktu_tempuh / 2)
        ];
    }

    private function generateLandmarkText($currentLokasi, $nextLokasi)
    {
        // Generate landmark berdasarkan jenis lokasi
        $landmarks = [
            'Masjid' => 'Area ibadah',
            'Pasar' => 'Pusat perdagangan',
            'Puskesmas' => 'Fasilitas kesehatan',
            'Kantor' => 'Gedung pemerintahan',
            'SDN' => 'Area pendidikan',
            'Taman' => 'Ruang terbuka hijau',
            'Pos' => 'Pos keamanan',
            'Warung' => 'Area kuliner',
            'Perpustakaan' => 'Fasilitas edukasi',
            'Lapangan' => 'Fasilitas olahraga',
            'Bengkel' => 'Area jasa',
            'Toko' => 'Area perdagangan',
            'Salon' => 'Area jasa kecantikan',
            'Musholla' => 'Tempat ibadah kecil'
        ];

        foreach ($landmarks as $keyword => $description) {
            if (str_contains($nextLokasi->nama, $keyword)) {
                return $description;
            }
        }

        return 'Lokasi umum';
    }

    private function getTurnDirection($segmentIndex, $fullRoute, $currentBearing)
    {
        if ($segmentIndex === 0) return 'Mulai';

        // Hitung bearing sebelumnya
        $prevCurrentId = $fullRoute[$segmentIndex - 1];
        $prevNextId = $fullRoute[$segmentIndex];

        $prevLokasi = Lokasi::find($prevCurrentId);
        $currentLokasi = Lokasi::find($prevNextId);

        if (!$prevLokasi || !$currentLokasi) return 'Lanjutkan';

        $prevBearing = $this->calculateBearing(
            $prevLokasi->latitude,
            $prevLokasi->longitude,
            $currentLokasi->latitude,
            $currentLokasi->longitude
        );

        // Hitung perbedaan bearing
        $bearingDiff = $currentBearing - $prevBearing;
        if ($bearingDiff > 180) $bearingDiff -= 360;
        if ($bearingDiff < -180) $bearingDiff += 360;

        // Tentukan arah berdasarkan perbedaan bearing
        if (abs($bearingDiff) < 30) return 'Lanjutkan lurus';
        if ($bearingDiff > 30) return 'Belok kanan';
        if ($bearingDiff < -30) return 'Belok kiri';

        return 'Lanjutkan';
    }

    // ... (methods calculateBearing, bearingToDirection, getDirectionIcon sama seperti sebelumnya)

    private function calculateBearing($lat1, $lon1, $lat2, $lon2)
    {
        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $deltaLon = deg2rad($lon2 - $lon1);

        $x = sin($deltaLon) * cos($lat2);
        $y = cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($deltaLon);

        $bearing = atan2($x, $y);
        $bearing = rad2deg($bearing);
        $bearing = ($bearing + 360) % 360;

        return $bearing;
    }

    private function bearingToDirection($bearing)
    {
        $directions = [
            'Utara' => [337.5, 22.5],
            'Timur Laut' => [22.5, 67.5],
            'Timur' => [67.5, 112.5],
            'Tenggara' => [112.5, 157.5],
            'Selatan' => [157.5, 202.5],
            'Barat Daya' => [202.5, 247.5],
            'Barat' => [247.5, 292.5],
            'Barat Laut' => [292.5, 337.5]
        ];

        foreach ($directions as $dir => $range) {
            if ($range[0] > $range[1]) {
                if ($bearing >= $range[0] || $bearing <= $range[1]) {
                    return $dir;
                }
            } else {
                if ($bearing >= $range[0] && $bearing <= $range[1]) {
                    return $dir;
                }
            }
        }

        return 'Utara';
    }

    private function getDirectionIcon($direction)
    {
        $icons = [
            'Utara' => 'arrow-up',
            'Timur Laut' => 'arrow-up-right',
            'Timur' => 'arrow-right',
            'Tenggara' => 'arrow-down-right',
            'Selatan' => 'arrow-down',
            'Barat Daya' => 'arrow-down-left',
            'Barat' => 'arrow-left',
            'Barat Laut' => 'arrow-up-left'
        ];

        return $icons[$direction] ?? 'arrow-up';
    }

    // ... (sisa method dijkstraFromDatabase tetap sama)
    private function dijkstraFromDatabase($asal, $tujuan)
    {
        // [Kode sama seperti sebelumnya]
        $semuaLokasi = Lokasi::where('aktif', true)->get()->keyBy('id');
        $lokasiIds = $semuaLokasi->keys()->toArray();

        $semuaRute = Rute::with(['lokasiAsal', 'lokasiTujuan'])->get();

        $graf = [];
        foreach ($lokasiIds as $id) {
            $graf[$id] = [];
        }

        foreach ($semuaRute as $rute) {
            $dari = $rute->lokasi_asal_id;
            $ke = $rute->lokasi_tujuan_id;
            $jarak = $rute->jarak;

            if (in_array($dari, $lokasiIds) && in_array($ke, $lokasiIds)) {
                $graf[$dari][$ke] = $jarak;
            }
        }

        $jarak = [];
        $sebelumnya = [];
        $belumDikunjungi = [];

        foreach ($lokasiIds as $id) {
            $jarak[$id] = $id == $asal ? 0 : INF;
            $sebelumnya[$id] = null;
            $belumDikunjungi[$id] = true;
        }

        while (!empty($belumDikunjungi)) {
            $current = null;
            $jarakTerkecil = INF;

            foreach ($belumDikunjungi as $id => $value) {
                if ($jarak[$id] < $jarakTerkecil) {
                    $jarakTerkecil = $jarak[$id];
                    $current = $id;
                }
            }

            if ($current === null || $jarakTerkecil === INF) {
                break;
            }

            unset($belumDikunjungi[$current]);

            if ($current == $tujuan) {
                break;
            }

            if (isset($graf[$current])) {
                foreach ($graf[$current] as $tetanggaId => $jarakKeTetangga) {
                    if (isset($belumDikunjungi[$tetanggaId])) {
                        $jarakBaru = $jarak[$current] + $jarakKeTetangga;

                        if ($jarakBaru < $jarak[$tetanggaId]) {
                            $jarak[$tetanggaId] = $jarakBaru;
                            $sebelumnya[$tetanggaId] = $current;
                        }
                    }
                }
            }
        }

        if ($jarak[$tujuan] === INF) {
            return null;
        }

        $jalur = [];
        $current = $tujuan;

        while ($current !== null) {
            array_unshift($jalur, $current);
            $current = $sebelumnya[$current];
        }

        $totalWaktu = 0;
        for ($i = 0; $i < count($jalur) - 1; $i++) {
            $ruteSegmen = Rute::where('lokasi_asal_id', $jalur[$i])
                ->where('lokasi_tujuan_id', $jalur[$i + 1])
                ->first();
            if ($ruteSegmen) {
                $totalWaktu += $ruteSegmen->waktu_tempuh;
            }
        }

        return [
            'jalur' => $jalur,
            'total_jarak' => round($jarak[$tujuan], 2),
            'total_waktu' => $totalWaktu,
        ];
    }

    public function riwayat()
    {
        $riwayat = PencarianRute::with(['lokasiAsal', 'lokasiTujuan'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('pencarian-rute.riwayat', compact('riwayat'));
    }

    public function detailRiwayat(PencarianRute $pencarianRute)
    {
        if ($pencarianRute->user_id !== auth()->id()) {
            abort(403);
        }

        $detailLokasi = Lokasi::whereIn('id', $pencarianRute->jalur_rute)->get()->keyBy('id');

        // Generate directions untuk riwayat
        $directions = $this->generateTurnByTurnDirections($pencarianRute->jalur_rute);

        return view('pencarian-rute.detail-riwayat', compact('pencarianRute', 'detailLokasi', 'directions'));
    }
}
