<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('jawaban_siswas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ujian_siswa_id')->constrained('ujian_siswas')->onDelete('cascade');
        $table->foreignId('soal_id')->constrained('soals')->onDelete('cascade');
        $table->enum('jawaban_dipilih', ['a', 'b', 'c', 'd', 'e'])->nullable(); // Nullable jika siswa tidak menjawab
        $table->boolean('is_correct')->default(false); // Acuan warna hijau (true) / merah (false)
        $table->timestamps();
    });
}
};
