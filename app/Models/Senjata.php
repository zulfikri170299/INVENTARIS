<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Senjata extends Model
{
    protected $fillable = [
        'satker_id',
        'jenis_senpi',
        'laras',
        'nup',
        'no_senpi',
        'kondisi',
        'penanggung_jawab',
        'nrp',
        'status_penyimpanan',
        'masa_berlaku_simsa',
        'keterangan',
    ];

    public function satker()
    {
        return $this->belongsTo(Satker::class);
    }
}
