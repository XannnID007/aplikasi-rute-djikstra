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

        // Implementasi algoritma Dijkstra dengan auto-calculate
        $hasil = $this->dijkstraAutoCalculate($lokasiAsal, $lokasiTujuan);

        if ($hasil === null) {
            return back()->with('error', 'Tidak ada rute yang tersedia dari lokasi asal ke lokasi tujuan.');
        }

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
     * Algoritma Dijkstra dengan Auto-Calculate Distance
     * Tidak perlu input rute manual, otomatis hitung dari koordinat GPS
     */
    private function dijkstraAutoCalculate($asal, $tujuan)
    {
        // Ambil semua lokasi aktif
        $semuaLokasi = Lokasi::where('aktif', true)->get()->keyBy('id');
        $lokasiIds = $semuaLokasi->keys()->toArray();

        // Inisialisasi
        $jarak = [];
        $sebelumnya = [];
        $belumDikunjungi = [];

        foreach ($lokasiIds as $id) {
            $jarak[$id] = $id == $asal ? 0 : INF;
            $sebelumnya[$id] = null;
            $belumDikunjungi[$id] = true;
        }

        while (!empty($belumDikunjungi)) {
            // Temukan node dengan jarak terkecil
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

            // Hapus dari belum dikunjungi
            unset($belumDikunjungi[$current]);

            // Jika sudah sampai tujuan, stop
            if ($current == $tujuan) {
                break;
            }

            // Update jarak ke semua tetangga (semua lokasi lain)
            $lokasiCurrent = $semuaLokasi[$current];

            foreach ($belumDikunjungi as $tetanggaId => $value) {
                $lokasiTetangga = $semuaLokasi[$tetanggaId];

                // Hitung jarak menggunakan Haversine Formula
                $jarakKeTetangga = $this->hitungJarakHaversine(
                    $lokasiCurrent->latitude,
                    $lokasiCurrent->longitude,
                    $lokasiTetangga->latitude,
                    $lokasiTetangga->longitude
                );

                $jarakBaru = $jarak[$current] + $jarakKeTetangga;

                if ($jarakBaru < $jarak[$tetanggaId]) {
                    $jarak[$tetanggaId] = $jarakBaru;
                    $sebelumnya[$tetanggaId] = $current;
                }
            }
        }

        // Jika tidak ada jalur
        if ($jarak[$tujuan] === INF) {
            return null;
        }

        // Rekonstruksi jalur
        $jalur = [];
        $current = $tujuan;

        while ($current !== null) {
            array_unshift($jalur, $current);
            $current = $sebelumnya[$current];
        }

        // Hitung total waktu (asumsi kecepatan 40 km/jam)
        $totalWaktu = round(($jarak[$tujuan] / 40) * 60); // Convert to minutes

        return [
            'jalur' => $jalur,
            'total_jarak' => round($jarak[$tujuan], 2),
            'total_waktu' => $totalWaktu,
        ];
    }

    /**
     * Hitung jarak antara dua koordinat menggunakan Haversine Formula
     * 
     * @param float $lat1 Latitude lokasi 1
     * @param float $lon1 Longitude lokasi 1
     * @param float $lat2 Latitude lokasi 2
     * @param float $lon2 Longitude lokasi 2
     * @return float Jarak dalam kilometer
     */
    private function hitungJarakHaversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius bumi dalam kilometer

        // Convert degrees to radians
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        // Haversine formula
        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance;
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
        // Pastikan user hanya bisa melihat riwayat sendiri
        if ($pencarianRute->user_id !== auth()->id()) {
            abort(403);
        }

        $detailLokasi = Lokasi::whereIn('id', $pencarianRute->jalur_rute)->get()->keyBy('id');

        return view('pencarian-rute.detail-riwayat', compact('pencarianRute', 'detailLokasi'));
    }
}
