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
    ];

    protected $casts = [
        'jarak' => 'decimal:2',
        'waktu_tempuh' => 'integer',
    ];

    public function lokasiAsal()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_asal_id');
    }

    public function lokasiTujuan()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_tujuan_id');
    }
}
