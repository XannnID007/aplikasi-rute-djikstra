<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PencarianRute extends Model
{
    use HasFactory;

    protected $table = 'pencarian_rute';

    protected $fillable = [
        'user_id',
        'lokasi_asal_id',
        'lokasi_tujuan_id',
        'jalur_rute',
        'total_jarak',
        'total_waktu',
    ];

    protected $casts = [
        'jalur_rute' => 'array',
        'total_jarak' => 'decimal:2',
        'total_waktu' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lokasiAsal()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_asal_id');
    }

    public function lokasiTujuan()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_tujuan_id');
    }
}
