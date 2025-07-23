<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;
use App\Models\Rute;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasi = Lokasi::paginate(10);
        return view('admin.lokasi.index', compact('lokasi'));
    }

    public function create()
    {
        return view('admin.lokasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        Lokasi::create($request->all());

        return redirect()->route('admin.lokasi.index')
            ->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function show(Lokasi $lokasi)
    {
        return view('admin.lokasi.show', compact('lokasi'));
    }

    public function edit(Lokasi $lokasi)
    {
        return view('admin.lokasi.edit', compact('lokasi'));
    }

    public function update(Request $request, Lokasi $lokasi)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $lokasi->update($request->all());

        return redirect()->route('admin.lokasi.index')
            ->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Lokasi $lokasi)
    {
        // Cek apakah lokasi sedang digunakan dalam rute
        if (Rute::where('lokasi_asal_id', $lokasi->id)->orWhere('lokasi_tujuan_id', $lokasi->id)->exists()) {
            return redirect()->route('admin.lokasi.index')
                ->with('error', 'Lokasi tidak dapat dihapus karena sedang digunakan dalam rute.');
        }

        $lokasi->delete();

        return redirect()->route('admin.lokasi.index')
            ->with('success', 'Lokasi berhasil dihapus.');
    }

    public function kelolaRute(Lokasi $lokasi)
    {
        $ruteAsal = Rute::where('lokasi_asal_id', $lokasi->id)->with('lokasiTujuan')->get();
        $semuaLokasi = Lokasi::where('id', '!=', $lokasi->id)->get();

        return view('admin.lokasi.kelola-rute', compact('lokasi', 'ruteAsal', 'semuaLokasi'));
    }

    public function simpanRute(Request $request, Lokasi $lokasi)
    {
        $request->validate([
            'lokasi_tujuan_id' => 'required|exists:lokasi,id',
            'jarak' => 'required|numeric|min:0|max:2', // Max 2km untuk area kelurahan
            'waktu_tempuh' => 'required|integer|min:0|max:20', // Max 20 menit untuk jarak pendek
        ]);

        // Cek apakah rute sudah ada
        $ruteAda = Rute::where('lokasi_asal_id', $lokasi->id)
            ->where('lokasi_tujuan_id', $request->lokasi_tujuan_id)
            ->exists();

        if ($ruteAda) {
            return redirect()->back()->with('error', 'Rute sudah ada.');
        }

        // Validasi jarak masuk akal untuk area kelurahan
        $lokasiTujuan = Lokasi::find($request->lokasi_tujuan_id);
        $jarakGPS = $this->hitungJarakHaversine(
            $lokasi->latitude,
            $lokasi->longitude,
            $lokasiTujuan->latitude,
            $lokasiTujuan->longitude
        );

        // Jarak input tidak boleh lebih dari 3x jarak GPS
        if ($request->jarak > ($jarakGPS * 3)) {
            return redirect()->back()->with('error', 'Jarak terlalu jauh dari koordinat GPS. Maksimal ' . round($jarakGPS * 3, 2) . ' km.');
        }

        Rute::create([
            'lokasi_asal_id' => $lokasi->id,
            'lokasi_tujuan_id' => $request->lokasi_tujuan_id,
            'jarak' => $request->jarak,
            'waktu_tempuh' => $request->waktu_tempuh,
        ]);

        return redirect()->back()->with('success', 'Rute berhasil ditambahkan.');
    }

    public function hapusRute(Rute $rute)
    {
        $rute->delete();
        return redirect()->back()->with('success', 'Rute berhasil dihapus.');
    }

    /**
     * Auto-generate routes dengan validasi untuk area kelurahan
     */
    public function autoGenerateRoutes()
    {
        $semuaLokasi = Lokasi::where('aktif', true)->get();
        $routesGenerated = 0;
        $routesSkipped = 0;

        foreach ($semuaLokasi as $lokasiAsal) {
            foreach ($semuaLokasi as $lokasiTujuan) {
                // Skip jika lokasi sama
                if ($lokasiAsal->id == $lokasiTujuan->id) {
                    continue;
                }

                // Skip jika rute sudah ada
                $ruteAda = Rute::where('lokasi_asal_id', $lokasiAsal->id)
                    ->where('lokasi_tujuan_id', $lokasiTujuan->id)
                    ->exists();

                if ($ruteAda) {
                    $routesSkipped++;
                    continue;
                }

                // Hitung jarak menggunakan Haversine Formula
                $jarak = $this->hitungJarakHaversine(
                    $lokasiAsal->latitude,
                    $lokasiAsal->longitude,
                    $lokasiTujuan->latitude,
                    $lokasiTujuan->longitude
                );

                // Hanya generate rute untuk jarak yang masuk akal (max 1.5km untuk area kelurahan)
                if ($jarak <= 1.5) {
                    // Hitung waktu tempuh (asumsi 15-20 km/jam untuk jalan lokal)
                    $waktuTempuh = max(1, round(($jarak / 18) * 60)); // Min 1 menit

                    // Buat rute baru
                    Rute::create([
                        'lokasi_asal_id' => $lokasiAsal->id,
                        'lokasi_tujuan_id' => $lokasiTujuan->id,
                        'jarak' => round($jarak, 2),
                        'waktu_tempuh' => $waktuTempuh,
                    ]);

                    $routesGenerated++;
                } else {
                    $routesSkipped++;
                }
            }
        }

        return redirect()->route('admin.lokasi.index')
            ->with('success', "Auto-generate selesai: {$routesGenerated} rute dibuat, {$routesSkipped} rute dilewati (jarak terlalu jauh atau sudah ada).");
    }

    /**
     * Hitung jarak menggunakan Haversine Formula
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

    /**
     * Analisis konektivitas rute
     */
    public function analisisKonektivitas()
    {
        $lokasi = Lokasi::where('aktif', true)->get();
        $rute = Rute::all();

        $analisis = [
            'total_lokasi' => $lokasi->count(),
            'total_rute' => $rute->count(),
            'lokasi_terisolasi' => [],
            'rata_rata_koneksi' => 0,
        ];

        // Cek lokasi yang terisolasi (tidak ada rute masuk atau keluar)
        foreach ($lokasi as $loc) {
            $ruteKeluar = $rute->where('lokasi_asal_id', $loc->id)->count();
            $ruteMasuk = $rute->where('lokasi_tujuan_id', $loc->id)->count();

            if ($ruteKeluar == 0 && $ruteMasuk == 0) {
                $analisis['lokasi_terisolasi'][] = $loc;
            }
        }

        $analisis['rata_rata_koneksi'] = $lokasi->count() > 0 ? round($rute->count() / $lokasi->count(), 1) : 0;

        return response()->json($analisis);
    }
}
