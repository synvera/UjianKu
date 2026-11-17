<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('ujian_siswas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ID Siswa
        $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->onDelete('cascade');
        $table->decimal('nilai', 5, 2)->default(0); // Contoh: 88.00 atau 100.00
        $table->enum('status', ['sedang_mengerjakan', 'selesai'])->default('sedang_mengerjakan');
        $table->timestamp('waktu_selesai')->nullable();
        $table->timestamps();
    });
}
};
