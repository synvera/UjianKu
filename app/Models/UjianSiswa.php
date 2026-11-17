<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UjianSiswa extends Model
{
    protected $fillable = [
        'user_id',
        'mata_pelajaran_id',
        'nilai',
        'status',
        'pelanggaran',
        'waktu_selesai',
    ];

    protected function casts(): array
    {
        return [
            'waktu_selesai' => 'datetime',
        ];
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function jawabanSiswas()
    {
        return $this->hasMany(JawabanSiswa::class);
    }
}