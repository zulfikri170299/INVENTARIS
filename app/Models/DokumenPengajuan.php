<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenPengajuan extends Model
{
    protected $table = 'dokumen_pengajuan';

    protected $fillable = [
        'pengajuan_berkas_id',
        'persyaratan_berkas_id',
        'file_path',
        'nama_file',
        'terverifikasi',
        'butuh_revisi',
    ];

    protected $casts = [
        'terverifikasi' => 'boolean',
        'butuh_revisi' => 'boolean',
    ];

    public function pengajuanBerkas()
    {
        return $this->belongsTo(PengajuanBerkas::class);
    }

    public function persyaratan()
    {
        return $this->belongsTo(PersyaratanBerkas::class, 'persyaratan_berkas_id');
    }
}
