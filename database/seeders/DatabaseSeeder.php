<?php

namespace Database\Seeders;

use App\Models\MataPelajaran;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $guru = User::create([
            'name' => 'Kahfi',
            'username' => 'kahfi',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'kelas' => null,
        ]);

        User::create([
            'name' => 'Gilang maulana akbar',
            'username' => 'gilang',
            'password' => Hash::make('siswa123'),
            'role' => 'siswa',
            'kelas' => 'VIII',
        ]);

        $daftarMapel = [
            'Matematika',
            'Bahasa Indonesia',
            'Bahasa Inggris',
            'Pemrograman Web',
            'Basis Data',
            'Pemrograman Berorientasi Objek',
            'Jaringan Komputer',
            'Sistem Operasi',
            'Produk Kreatif dan Kewirausahaan',
            'Pendidikan Kewarganegaraan',
        ];

        $mapelPertama = null;

        foreach ($daftarMapel as $index => $namaMapel) {
            $mapel = MataPelajaran::create([
                'user_id' => $guru->id,
                'nama_mapel' => $namaMapel,
                'jumlah_soal_tampil' => 25,
            ]);

            if ($index === 0) {
                $mapelPertama = $mapel;
            }
        }

        if ($mapelPertama) {
            for ($i = 1; $i <= 5; $i++) {
                Soal::create([
                    'mata_pelajaran_id' => $mapelPertama->id,
                    'pertanyaan' => "Contoh pertanyaan dummy nomor {$i} untuk mata pelajaran {$mapelPertama->nama_mapel}?",
                    'opsi_a' => 'Pilihan Jawaban A',
                    'opsi_b' => 'Pilihan Jawaban B',
                    'opsi_c' => 'Pilihan Jawaban C',
                    'opsi_d' => 'Pilihan Jawaban D',
                    'opsi_e' => 'Pilihan Jawaban E',
                    'kunci_jawaban' => 'a',
                ]);
            }
        }
    }
}