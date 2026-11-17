<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KodeUjian extends Model
{
    protected $fillable = [
        'mata_pelajaran_id',
        'kode',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'expired_at' => 'datetime',
        ];
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }
}