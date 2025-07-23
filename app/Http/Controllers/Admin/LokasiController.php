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
            'jarak' => 'required|numeric|min:0',
            'waktu_tempuh' => 'required|integer|min:0',
        ]);

        // Cek apakah rute sudah ada
        $ruteAda = Rute::where('lokasi_asal_id', $lokasi->id)
            ->where('lokasi_tujuan_id', $request->lokasi_tujuan_id)
            ->exists();

        if ($ruteAda) {
            return redirect()->back()->with('error', 'Rute sudah ada.');
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

    public function autoGenerateRoutes()
    {
        $semuaLokasi = Lokasi::where('aktif', true)->get();
        $routesGenerated = 0;

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
                    continue;
                }

                // Hitung jarak menggunakan Haversine Formula
                $jarak = $this->hitungJarakHaversine(
                    $lokasiAsal->latitude,
                    $lokasiAsal->longitude,
                    $lokasiTujuan->latitude,
                    $lokasiTujuan->longitude
                );

                // Hitung waktu tempuh (asumsi 40 km/jam)
                $waktuTempuh = round(($jarak / 40) * 60);

                // Buat rute baru
                Rute::create([
                    'lokasi_asal_id' => $lokasiAsal->id,
                    'lokasi_tujuan_id' => $lokasiTujuan->id,
                    'jarak' => round($jarak, 2),
                    'waktu_tempuh' => $waktuTempuh,
                ]);

                $routesGenerated++;
            }
        }

        return redirect()->route('admin.lokasi.index')
            ->with('success', "Berhasil generate {$routesGenerated} rute otomatis dari koordinat GPS.");
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
}
