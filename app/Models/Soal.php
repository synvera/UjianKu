<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $fillable = [
        'mata_pelajaran_id',
        'pertanyaan',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'opsi_e',
        'kunci_jawaban',
    ];

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function jawabanSiswas()
    {
        return $this->hasMany(JawabanSiswa::class);
    }
}