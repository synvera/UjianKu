<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('kode_ujians', function (Blueprint $table) {
        $table->id();
        $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->onDelete('cascade');
        $table->string('kode', 10)->unique();
        $table->timestamp('expired_at'); // Akan diisi waktu pembuatan + 5 menit
        $table->timestamps();
    });
}
};
