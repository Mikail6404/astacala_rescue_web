<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelaporans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_team_pelapor');
            $table->integer('jumlah_personel');
            $table->string('informasi_singkat_bencana');
            $table->string('lokasi_bencana');
            $table->string('foto_lokasi_bencana')->nullable(); // menyimpan path gambar
            $table->string('titik_kordinat_lokasi_bencana');
            $table->string('skala_bencana');
            $table->integer('jumlah_korban');
            $table->string('bukti_surat_perintah_tugas')->nullable(); // menyimpan path PDF
            $table->text('deskripsi_terkait_data_lainya')->nullable();

            // Foreign key
            $table->unsignedBigInteger('pelapor_pengguna_id');
            $table->foreign('pelapor_pengguna_id')->references('id')->on('penggunas')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelaporans');
    }
};
