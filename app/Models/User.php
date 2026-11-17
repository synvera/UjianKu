<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'kelas',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function mataPelajarans()
    {
        return $this->hasMany(MataPelajaran::class);
    }

    public function ujianSiswas()
    {
        return $this->hasMany(UjianSiswa::class);
    }
}