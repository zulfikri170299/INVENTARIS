<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanBerkas extends Model
{
    protected $table = 'pengajuan_berkas';

    protected $fillable = [
        'kategori',
        'user_id',
        'satker_id',
        'judul',
        'keterangan',
        'status',
        'catatan_super_admin',
        'berkas_final',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function satker()
    {
        return $this->belongsTo(Satker::class);
    }

    public function dokumen()
    {
        return $this->hasMany(DokumenPengajuan::class);
    }

    public function riwayat()
    {
        return $this->hasMany(RiwayatPengajuan::class)->orderBy('created_at', 'asc');
    }

    public function getKategoriLabelAttribute()
    {
        return match($this->kategori) {
            'penghapusan' => 'Penghapusan Barang',
            'penetapan_status' => 'Penetapan Status Penggunaan',
            default => $this->kategori,
        };
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

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'diajukan' => 'yellow',
            'diterima' => 'blue',
            'diproses' => 'cyan',
            'dikembalikan' => 'red',
            'naik_ke_kapolda' => 'purple',
            'ditandatangani' => 'indigo',
            'selesai' => 'green',
            default => 'gray',
        };
    }
}
