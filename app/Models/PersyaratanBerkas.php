<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersyaratanBerkas extends Model
{
    protected $table = 'persyaratan_berkas';

    protected $fillable = [
        'kategori',
        'nama_persyaratan',
        'deskripsi',
        'wajib',
        'urutan',
        'file_contoh',
        'nama_file_contoh',
    ];

    protected $casts = [
        'wajib' => 'boolean',
    ];

    public function dokumenPengajuan()
    {
        return $this->hasMany(DokumenPengajuan::class);
    }
}
