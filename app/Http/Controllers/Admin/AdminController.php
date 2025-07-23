<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Lokasi;
use App\Models\PencarianRute;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $data = [
            'total_pengguna' => User::where('peran', 'pengguna')->count(),
            'total_lokasi' => Lokasi::count(),
            'total_pencarian' => PencarianRute::count(),
            'pencarian_hari_ini' => PencarianRute::whereDate('created_at', today())->count(),
        ];

        return view('admin.dashboard', compact('data'));
    }

    public function daftarPengguna()
    {
        // PERBAIKAN: Load relasi dengan eager loading
        $pengguna = User::where('peran', 'pengguna')
            ->with(['pencarianRute' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->paginate(10);

        return view('admin.pengguna.index', compact('pengguna'));
    }

    public function riwayatPencarian()
    {
        $riwayat = PencarianRute::with(['user', 'lokasiAsal', 'lokasiTujuan'])
            ->latest()
            ->paginate(10);

        return view('admin.riwayat.index', compact('riwayat'));
    }
}
