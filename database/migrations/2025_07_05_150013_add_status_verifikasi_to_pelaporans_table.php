<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pelaporans', function (Blueprint $table) {
            $table->enum('status_verifikasi', ['PENDING', 'DITERIMA', 'DITOLAK'])
                ->default('PENDING')
                ->after('deskripsi_terkait_data_lainya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelaporans', function (Blueprint $table) {
            //
        });
    }
};
