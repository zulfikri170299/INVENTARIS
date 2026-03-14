<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPengajuan extends Model
{
    protected $table = 'riwayat_pengajuan';

    protected $fillable = [
        'pengajuan_berkas_id',
        'user_id',
        'status',
        'catatan',
    ];

    public function pengajuanBerkas()
    {
        return $this->belongsTo(PengajuanBerkas::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'diajukan' => 'Diajukan',
            'diterima' => 'Diterima',
            'diproses' => 'Diproses',
            'dikembalikan' => 'Dikembalikan',
            'naik_ke_kapolda' => 'Naik ke Kapolda',
            'ditandatangani' => 'Ditandatangani Kapolda',
            'selesai' => 'Selesai',
            default => $this->status,
        };
    }
}
