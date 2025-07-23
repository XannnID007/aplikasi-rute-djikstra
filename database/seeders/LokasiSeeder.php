<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Lokasi;
use App\Models\Rute;

class LokasiSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks untuk menghindari error constraint
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Hapus data lama dengan aman
        Rute::truncate();
        Lokasi::truncate();

        // Reset auto increment
        DB::statement('ALTER TABLE rute AUTO_INCREMENT = 1;');
        DB::statement('ALTER TABLE lokasi AUTO_INCREMENT = 1;');

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Data lokasi dalam Kelurahan Pasirkaliki, Bandung
        $lokasi = [
            [
                'nama' => 'Masjid Agung Pasirkaliki',
                'alamat' => 'Jl. Pasirkaliki Raya No.1, Pasirkaliki, Cicendo, Kota Bandung',
                'latitude' => -6.903194,
                'longitude' => 107.588889,
                'aktif' => true,
            ],
            [
                'nama' => 'Pasar Pasirkaliki',
                'alamat' => 'Jl. Pasirkaliki No.25, Pasirkaliki, Cicendo, Kota Bandung',
                'latitude' => -6.904167,
                'longitude' => 107.589167,
                'aktif' => true,
            ],
            [
                'nama' => 'SDN Pasirkaliki 1',
                'alamat' => 'Jl. Pasirkaliki Utara No.15, Pasirkaliki, Cicendo, Kota Bandung',
                'latitude' => -6.902500,
                'longitude' => 107.588056,
                'aktif' => true,
            ],
            [
                'nama' => 'Puskesmas Pasirkaliki',
                'alamat' => 'Jl. Pasirkaliki Selatan No.10, Pasirkaliki, Cicendo, Kota Bandung',
                'latitude' => -6.904722,
                'longitude' => 107.589444,
                'aktif' => true,
            ],
            [
                'nama' => 'Taman Kelurahan Pasirkaliki',
                'alamat' => 'Jl. Taman Pasirkaliki, Pasirkaliki, Cicendo, Kota Bandung',
                'latitude' => -6.903611,
                'longitude' => 107.588333,
                'aktif' => true,
            ],
            [
                'nama' => 'Kantor Kelurahan Pasirkaliki',
                'alamat' => 'Jl. Pasirkaliki Tengah No.20, Pasirkaliki, Cicendo, Kota Bandung',
                'latitude' => -6.903889,
                'longitude' => 107.588611,
                'aktif' => true,
            ],
            [
                'nama' => 'Pos Ronda RT 01',
                'alamat' => 'Jl. Pasirkaliki Gang Mesjid, Pasirkaliki, Cicendo, Kota Bandung',
                'latitude' => -6.903056,
                'longitude' => 107.588750,
                'aktif' => true,
            ],
            [
                'nama' => 'Warung Nasi Ibu Sari',
                'alamat' => 'Jl. Pasirkaliki No.35, Pasirkaliki, Cicendo, Kota Bandung',
                'latitude' => -6.904444,
                'longitude' => 107.589000,
                'aktif' => true,
            ],
            [
                'nama' => 'Perpustakaan Kelurahan',
                'alamat' => 'Jl. Pasirkaliki Barat No.8, Pasirkaliki, Cicendo, Kota Bandung',
                'latitude' => -6.903333,
                'longitude' => 107.588194,
                'aktif' => true,
            ],
            [
                'nama' => 'Lapangan Futsal Pasirkaliki',
                'alamat' => 'Jl. Pasirkaliki Timur No.12, Pasirkaliki, Cicendo, Kota Bandung',
                'latitude' => -6.903750,
                'longitude' => 107.589028,
                'aktif' => true,
            ],
            [
                'nama' => 'Bengkel Motor Pak Joko',
                'alamat' => 'Jl. Pasirkaliki No.42, Pasirkaliki, Cicendo, Kota Bandung',
                'latitude' => -6.904611,
                'longitude' => 107.589306,
                'aktif' => true,
            ],
            [
                'nama' => 'Toko Sembako Berkah',
                'alamat' => 'Jl. Pasirkaliki Gang 1 No.5, Pasirkaliki, Cicendo, Kota Bandung',
                'latitude' => -6.903472,
                'longitude' => 107.588472,
                'aktif' => true,
            ],
            [
                'nama' => 'Salon Cantik Indah',
                'alamat' => 'Jl. Pasirkaliki No.28, Pasirkaliki, Cicendo, Kota Bandung',
                'latitude' => -6.904056,
                'longitude' => 107.588944,
                'aktif' => true,
            ],
            [
                'nama' => 'Pos Kamling RT 05',
                'alamat' => 'Jl. Pasirkaliki Gang 3, Pasirkaliki, Cicendo, Kota Bandung',
                'latitude' => -6.904306,
                'longitude' => 107.589222,
                'aktif' => true,
            ],
            [
                'nama' => 'Musholla Al-Ikhlas',
                'alamat' => 'Jl. Pasirkaliki Gang Musholla, Pasirkaliki, Cicendo, Kota Bandung',
                'latitude' => -6.903139,
                'longitude' => 107.588556,
                'aktif' => true,
            ]
        ];

        // Simpan lokasi dengan cara yang aman
        foreach ($lokasi as $data) {
            Lokasi::create($data);
        }

        // Tunggu sebentar untuk memastikan data lokasi tersimpan
        sleep(1);

        // Buat rute yang realistis antar lokasi berdasarkan jalanan
        $rute = [
            // Dari Masjid Agung (1) ke lokasi lain
            [1, 7, 0.12, 2],   // ke Pos Ronda RT 01 (jalan dekat)
            [1, 6, 0.18, 3],   // ke Kantor Kelurahan
            [1, 15, 0.15, 2],  // ke Musholla Al-Ikhlas

            // Dari Pasar (2) ke lokasi sekitar
            [2, 8, 0.25, 4],   // ke Warung Nasi
            [2, 4, 0.20, 3],   // ke Puskesmas
            [2, 13, 0.22, 3],  // ke Salon Cantik
            [2, 11, 0.28, 4],  // ke Bengkel Motor

            // Dari SDN (3) ke lokasi dekat
            [3, 9, 0.30, 5],   // ke Perpustakaan
            [3, 5, 0.25, 4],   // ke Taman Kelurahan
            [3, 7, 0.20, 3],   // ke Pos Ronda RT 01

            // Dari Puskesmas (4) ke lokasi sekitar
            [4, 14, 0.18, 3],  // ke Pos Kamling RT 05
            [4, 11, 0.22, 3],  // ke Bengkel Motor
            [4, 10, 0.24, 4],  // ke Lapangan Futsal

            // Dari Taman (5) ke lokasi terdekat
            [5, 6, 0.15, 2],   // ke Kantor Kelurahan
            [5, 12, 0.20, 3],  // ke Toko Sembako
            [5, 10, 0.25, 4],  // ke Lapangan Futsal

            // Dari Kantor Kelurahan (6) - hub utama
            [6, 5, 0.15, 2],   // ke Taman
            [6, 7, 0.12, 2],   // ke Pos Ronda
            [6, 13, 0.20, 3],  // ke Salon
            [6, 12, 0.18, 3],  // ke Toko Sembako

            // Dari Pos Ronda RT 01 (7)
            [7, 15, 0.10, 1],  // ke Musholla (sangat dekat)
            [7, 12, 0.22, 3],  // ke Toko Sembako

            // Dari Warung Nasi (8)
            [8, 13, 0.15, 2],  // ke Salon
            [8, 14, 0.20, 3],  // ke Pos Kamling

            // Dari Perpustakaan (9)
            [9, 12, 0.18, 3],  // ke Toko Sembako
            [9, 15, 0.25, 4],  // ke Musholla

            // Dari Lapangan Futsal (10)
            [10, 11, 0.20, 3], // ke Bengkel
            [10, 14, 0.18, 3], // ke Pos Kamling

            // Dari Bengkel (11)
            [11, 14, 0.12, 2], // ke Pos Kamling (dekat)

            // Dari Toko Sembako (12)
            [12, 15, 0.20, 3], // ke Musholla

            // Dari Salon (13)
            [13, 8, 0.15, 2],  // ke Warung (balik)
            [13, 10, 0.25, 4], // ke Lapangan

            // Dari Pos Kamling (14)
            [14, 11, 0.12, 2], // ke Bengkel (balik)

            // Rute balik untuk semua koneksi
            [7, 1, 0.12, 2],
            [6, 1, 0.18, 3],
            [15, 1, 0.15, 2],
            [8, 2, 0.25, 4],
            [4, 2, 0.20, 3],
            [13, 2, 0.22, 3],
            [11, 2, 0.28, 4],
            [9, 3, 0.30, 5],
            [5, 3, 0.25, 4],
            [7, 3, 0.20, 3],
            [14, 4, 0.18, 3],
            [11, 4, 0.22, 3],
            [10, 4, 0.24, 4],
            [6, 5, 0.15, 2],
            [12, 5, 0.20, 3],
            [10, 5, 0.25, 4],
            [5, 6, 0.15, 2],
            [7, 6, 0.12, 2],
            [13, 6, 0.20, 3],
            [12, 6, 0.18, 3],
            [15, 7, 0.10, 1],
            [12, 7, 0.22, 3],
            [13, 8, 0.15, 2],
            [14, 8, 0.20, 3],
            [12, 9, 0.18, 3],
            [15, 9, 0.25, 4],
            [11, 10, 0.20, 3],
            [14, 10, 0.18, 3],
            [14, 11, 0.12, 2],
            [15, 12, 0.20, 3],
            [8, 13, 0.15, 2],
            [10, 13, 0.25, 4],
            [11, 14, 0.12, 2],

            // Koneksi tambahan untuk membuat jalur alternatif
            [1, 3, 0.35, 6],   // Masjid ke SDN (jalan alternatif)
            [3, 1, 0.35, 6],
            [2, 6, 0.40, 7],   // Pasar ke Kantor Kelurahan
            [6, 2, 0.40, 7],
            [4, 8, 0.30, 5],   // Puskesmas ke Warung
            [8, 4, 0.30, 5],
            [9, 5, 0.28, 5],   // Perpustakaan ke Taman
            [5, 9, 0.28, 5],
        ];

        // Simpan rute dengan validasi
        foreach ($rute as $data) {
            try {
                // Pastikan lokasi asal dan tujuan ada
                $lokasiAsal = Lokasi::find($data[0]);
                $lokasiTujuan = Lokasi::find($data[1]);

                if ($lokasiAsal && $lokasiTujuan) {
                    Rute::create([
                        'lokasi_asal_id' => $data[0],
                        'lokasi_tujuan_id' => $data[1],
                        'jarak' => $data[2],
                        'waktu_tempuh' => $data[3],
                    ]);
                }
            } catch (\Exception $e) {
                // Skip rute yang bermasalah
                continue;
            }
        }

        // Output informasi
        $totalLokasi = Lokasi::count();
        $totalRute = Rute::count();

        echo "✅ Seeder berhasil: {$totalLokasi} lokasi, {$totalRute} rute\n";
    }
}
