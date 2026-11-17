<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $fillable = [
        'user_id',
        'nama_mapel',
        'jumlah_soal_tampil',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function soals()
    {
        return $this->hasMany(Soal::class);
    }

    public function kodeUjians()
    {
        return $this->hasMany(KodeUjian::class);
    }

    public function ujianSiswas()
    {
        return $this->hasMany(UjianSiswa::class);
    }
}