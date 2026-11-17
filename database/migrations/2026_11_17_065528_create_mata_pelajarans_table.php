<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('mata_pelajarans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ID Guru/Admin pembuat mapel
        $table->string('nama_mapel');
        $table->integer('jumlah_soal_tampil')->default(25);
        $table->timestamps();
    });
}
};
