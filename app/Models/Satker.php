<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Kendaraan;
use App\Models\Senjata;
use App\Models\Alsus;
use App\Models\Alsintor;

class Satker extends Model
{
    protected $fillable = ['nama_satker'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function kendaraans()
    {
        return $this->hasMany(Kendaraan::class);
    }

    public function senjatas()
    {
        return $this->hasMany(Senjata::class);
    }

    public function alsuses()
    {
        return $this->hasMany(Alsus::class);
    }

    public function alsintors()
    {
        return $this->hasMany(Alsintor::class);
    }
}
