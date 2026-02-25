<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $fillable = [
        'jenis_roda',
        'satker_id',
        'jenis_kendaraan',
        'nup',
        'no_rangka',
        'nopol',
        'kondisi',
        'bahan_bakar',
        'penanggung_jawab',
        'nrp',
        'keterangan',
    ];

    public function satker()
    {
        return $this->belongsTo(Satker::class);
    }
}
