<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';

    protected $fillable = [
        'nama',
        'alamat',
        'latitude',
        'longitude',
        'aktif',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'aktif' => 'boolean',
    ];

    public function ruteAsal()
    {
        return $this->hasMany(Rute::class, 'lokasi_asal_id');
    }

    public function ruteTujuan()
    {
        return $this->hasMany(Rute::class, 'lokasi_tujuan_id');
    }

    public function pencarianRuteAsal()
    {
        return $this->hasMany(PencarianRute::class, 'lokasi_asal_id');
    }

    public function pencarianRuteTujuan()
    {
        return $this->hasMany(PencarianRute::class, 'lokasi_tujuan_id');
    }
}
