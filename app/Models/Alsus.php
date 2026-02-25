<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alsus extends Model
{
    protected $fillable = [
        'satker_id',
        'jenis_barang',
        'nup',
        'kondisi',
        'keterangan',
    ];

    public function satker()
    {
        return $this->belongsTo(Satker::class);
    }
}
