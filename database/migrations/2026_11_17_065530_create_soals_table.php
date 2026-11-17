<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('soals', function (Blueprint $table) {
        $table->id();
        $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->onDelete('cascade');
        $table->longText('pertanyaan'); // Mendukung teks panjang, HTML <img>, dan <table>
        $table->text('opsi_a');
        $table->text('opsi_b');
        $table->text('opsi_c');
        $table->text('opsi_d');
        $table->text('opsi_e')->nullable(); // Opsional jika hanya pakai opsi A-D
        $table->enum('kunci_jawaban', ['a', 'b', 'c', 'd', 'e']);
        $table->timestamps();
    });
}
};
