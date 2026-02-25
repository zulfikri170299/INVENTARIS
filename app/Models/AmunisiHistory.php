<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmunisiHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'satker_id',
        'nama_personel',
        'pangkat_nrp',
        'jenis_amunisi',
        'jumlah',
        'tanggal',
        'keterangan',
    ];

    public function satker()
    {
        return $this->belongsTo(Satker::class);
    }
}
