<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lokasi;
use App\Models\Rute;

class LokasiSeeder extends Seeder
{
    public function run()
    {
        // Buat beberapa lokasi contoh di Bandung
        $lokasi = [
            [
                'nama' => 'Bandung Kota',
                'alamat' => 'Jl. Asia Afrika, Bandung',
                'latitude' => -6.921389,
                'longitude' => 107.607222,
            ],
            [
                'nama' => 'Dago',
                'alamat' => 'Jl. Ir. H. Djuanda, Bandung',
                'latitude' => -6.893056,
                'longitude' => 107.613889,
            ],
            [
                'nama' => 'Cihampelas',
                'alamat' => 'Jl. Cihampelas, Bandung',
                'latitude' => -6.898889,
                'longitude' => 107.598889,
            ],
            [
                'nama' => 'Pasteur',
                'alamat' => 'Jl. Dr. Djunjunan, Bandung',
                'latitude' => -6.893889,
                'longitude' => 107.576111,
            ],
            [
                'nama' => 'Cicendo',
                'alamat' => 'Jl. Cicendo, Bandung',
                'latitude' => -6.903056,
                'longitude' => 107.588889,
            ],
            [
                'nama' => 'Buah Batu',
                'alamat' => 'Jl. Buah Batu, Bandung',
                'latitude' => -6.968889,
                'longitude' => 107.632222,
            ],
        ];

        foreach ($lokasi as $data) {
            Lokasi::create($data);
        }

        // Buat rute antar lokasi
        $rute = [
            // Dari Bandung Kota
            [1, 2, 3.5, 15], // Ke Dago
            [1, 3, 2.8, 12], // Ke Cihampelas
            [1, 5, 2.2, 10], // Ke Cicendo
            [1, 6, 8.5, 25], // Ke Buah Batu

            // Dari Dago
            [2, 1, 3.5, 15], // Ke Bandung Kota
            [2, 3, 1.8, 8],  // Ke Cihampelas
            [2, 4, 4.2, 18], // Ke Pasteur

            // Dari Cihampelas
            [3, 1, 2.8, 12], // Ke Bandung Kota
            [3, 2, 1.8, 8],  // Ke Dago
            [3, 4, 2.5, 11], // Ke Pasteur
            [3, 5, 1.5, 7],  // Ke Cicendo

            // Dari Pasteur
            [4, 2, 4.2, 18], // Ke Dago
            [4, 3, 2.5, 11], // Ke Cihampelas
            [4, 5, 1.8, 8],  // Ke Cicendo

            // Dari Cicendo
            [5, 1, 2.2, 10], // Ke Bandung Kota
            [5, 3, 1.5, 7],  // Ke Cihampelas
            [5, 4, 1.8, 8],  // Ke Pasteur
            [5, 6, 6.5, 22], // Ke Buah Batu

            // Dari Buah Batu
            [6, 1, 8.5, 25], // Ke Bandung Kota
            [6, 5, 6.5, 22], // Ke Cicendo
        ];

        foreach ($rute as $data) {
            Rute::create([
                'lokasi_asal_id' => $data[0],
                'lokasi_tujuan_id' => $data[1],
                'jarak' => $data[2],
                'waktu_tempuh' => $data[3],
            ]);
        }
    }
}
