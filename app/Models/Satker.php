<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Kendaraan;
use App\Models\Senjata;
use App\Models\Alsus;
use App\Models\Alsintor;
use App\Models\Amunisi;

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

    public function amunisis()
    {
        return $this->hasMany(Amunisi::class);
    }
}
