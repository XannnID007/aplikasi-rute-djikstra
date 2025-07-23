<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rute extends Model
{
    use HasFactory;

    protected $table = 'rute';

    protected $fillable = [
        'lokasi_asal_id',
        'lokasi_tujuan_id',
        'jarak',
        'waktu_tempuh',
        'instruksi_arah',
        'landmark',
        'jenis_jalan',
    ];

    protected $casts = [
        'jarak' => 'decimal:2',
        'waktu_tempuh' => 'integer',
        'instruksi_arah' => 'array', // JSON untuk menyimpan instruksi detail
    ];

    public function lokasiAsal()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_asal_id');
    }

    public function lokasiTujuan()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_tujuan_id');
    }

    /**
     * Generate instruksi arah berdasarkan koordinat
     */
    public function generateDirections()
    {
        $asal = $this->lokasiAsal;
        $tujuan = $this->lokasiTujuan;

        if (!$asal || !$tujuan) return null;

        // Hitung bearing (arah kompas)
        $bearing = $this->calculateBearing(
            $asal->latitude,
            $asal->longitude,
            $tujuan->latitude,
            $tujuan->longitude
        );

        // Convert bearing ke arah
        $direction = $this->bearingToDirection($bearing);

        // Generate instruksi berdasarkan nama lokasi dan jarak
        return $this->generateInstructionText($direction, $this->jarak);
    }

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
            if ($range[0] > $range[1]) { // Handle wrap around (North)
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

    private function generateInstructionText($direction, $distance)
    {
        $instructions = [];

        // Instruksi awal
        $instructions[] = [
            'type' => 'start',
            'text' => "Mulai perjalanan dari {$this->lokasiAsal->nama}",
            'distance' => 0,
            'icon' => 'play'
        ];

        // Instruksi jalan berdasarkan nama lokasi dan landmark
        $landmarks = $this->detectLandmarks();

        if (!empty($landmarks)) {
            foreach ($landmarks as $landmark) {
                $instructions[] = $landmark;
            }
        }

        // Instruksi umum berdasarkan arah
        $instructions[] = [
            'type' => 'continue',
            'text' => "Lanjutkan ke arah {$direction} sejauh {$distance} km",
            'distance' => $distance * 1000, // Convert to meters
            'icon' => $this->getDirectionIcon($direction)
        ];

        // Instruksi tiba
        $instructions[] = [
            'type' => 'arrive',
            'text' => "Tiba di {$this->lokasiTujuan->nama}",
            'distance' => $distance * 1000,
            'icon' => 'flag'
        ];

        return $instructions;
    }

    private function detectLandmarks()
    {
        $landmarks = [];
        $asal = $this->lokasiAsal->nama;
        $tujuan = $this->lokasiTujuan->nama;

        // Landmark detection berdasarkan nama lokasi
        $landmarkRules = [
            // Dari tempat ibadah
            'Masjid' => [
                'landmark' => 'Keluar dari area masjid',
                'instruction' => 'Keluar dari kompleks masjid menuju jalan utama',
                'icon' => 'mosque'
            ],
            'Musholla' => [
                'landmark' => 'Keluar dari musholla',
                'instruction' => 'Keluar dari area musholla',
                'icon' => 'mosque'
            ],

            // Dari fasilitas umum
            'Pasar' => [
                'landmark' => 'Keluar dari area pasar',
                'instruction' => 'Keluar dari kompleks pasar menuju jalan raya',
                'icon' => 'store'
            ],
            'Puskesmas' => [
                'landmark' => 'Keluar dari puskesmas',
                'instruction' => 'Keluar dari halaman puskesmas',
                'icon' => 'hospital'
            ],
            'Kantor Kelurahan' => [
                'landmark' => 'Keluar dari kantor kelurahan',
                'instruction' => 'Keluar dari kompleks kantor kelurahan',
                'icon' => 'building'
            ],

            // Landmark jalan
            'Gang' => [
                'landmark' => 'Masuk/keluar gang',
                'instruction' => 'Belok masuk ke gang',
                'icon' => 'turn-right'
            ],
            'Raya' => [
                'landmark' => 'Jalan raya',
                'instruction' => 'Lurus di jalan raya',
                'icon' => 'arrow-up'
            ]
        ];

        // Check landmarks dari lokasi asal
        foreach ($landmarkRules as $keyword => $rule) {
            if (str_contains($asal, $keyword)) {
                $landmarks[] = [
                    'type' => 'landmark',
                    'text' => $rule['instruction'],
                    'distance' => 50, // 50 meter from start
                    'icon' => $rule['icon']
                ];
                break;
            }
        }

        // Landmark berdasarkan jenis rute
        $distance = $this->jarak;

        if ($distance > 0.2) { // Jika lebih dari 200m
            $landmarks[] = [
                'type' => 'waypoint',
                'text' => 'Lanjutkan lurus di Jl. Pasirkaliki',
                'distance' => $distance * 500, // Mid-point
                'icon' => 'arrow-up'
            ];
        }

        // Landmark menuju tujuan
        foreach ($landmarkRules as $keyword => $rule) {
            if (str_contains($tujuan, $keyword)) {
                $landmarks[] = [
                    'type' => 'approach',
                    'text' => "Belok menuju {$tujuan}",
                    'distance' => $distance * 800, // 80% of the way
                    'icon' => $this->getTurnDirection($asal, $tujuan)
                ];
                break;
            }
        }

        return $landmarks;
    }

    private function getTurnDirection($asal, $tujuan)
    {
        // Simple logic untuk menentukan arah belok
        $turnRules = [
            'Utara' => 'turn-left',
            'Selatan' => 'turn-right',
            'Timur' => 'turn-right',
            'Barat' => 'turn-left',
            'Gang' => 'turn-right' // Biasanya gang di kanan
        ];

        foreach ($turnRules as $keyword => $icon) {
            if (str_contains($tujuan, $keyword)) {
                return $icon;
            }
        }

        return 'arrow-up';
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
}
