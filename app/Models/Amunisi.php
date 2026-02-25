<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amunisi extends Model
{
    protected $fillable = [
        'satker_id',
        'jenis_amunisi',
        'jumlah',
        'status_penyimpanan',
        'keterangan',
    ];

    public function satker()
    {
        return $this->belongsTo(Satker::class);
    }
}
