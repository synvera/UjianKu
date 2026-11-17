<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanSiswa extends Model
{
    protected $fillable = [
        'ujian_siswa_id',
        'soal_id',
        'jawaban_dipilih',
        'is_correct',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    public function ujianSiswa()
    {
        return $this->belongsTo(UjianSiswa::class);
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }
}