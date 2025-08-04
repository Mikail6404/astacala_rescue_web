<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelaporan extends Model
{
    protected $table = 'pelaporans'; // Nama tabel di database

    protected $fillable = [
        'nama_team_pelapor',
        'jumlah_personel',
        'informasi_singkat_bencana',
        'lokasi_bencana',
        'foto_lokasi_bencana',
        'titik_kordinat_lokasi_bencana',
        'skala_bencana',
        'jumlah_korban',
        'bukti_surat_perintah_tugas',
        'deskripsi_terkait_data_lainya',
        'pelapor_pengguna_id',
        'status_notifikasi',
        'status_verifikasi'
    ];

    public $timestamps = true;

    // Relasi ke tabel penggunas
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pelapor_pengguna_id');
    }
}