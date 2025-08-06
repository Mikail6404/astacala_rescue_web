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
        'status_verifikasi',
    ];

    public $timestamps = true;

    // Relasi ke tabel penggunas
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pelapor_pengguna_id');
    }

    // Methods for unified backend integration
    public function toUnifiedFormat()
    {
        return [
            'id' => $this->id,
            'team_name' => $this->nama_team_pelapor,
            'personnel_count' => $this->jumlah_personel,
            'brief_info' => $this->informasi_singkat_bencana,
            'location' => $this->lokasi_bencana,
            'photo' => $this->foto_lokasi_bencana,
            'coordinates' => $this->titik_kordinat_lokasi_bencana,
            'scale' => $this->skala_bencana,
            'casualties' => $this->jumlah_korban,
            'task_document' => $this->bukti_surat_perintah_tugas,
            'additional_info' => $this->deskripsi_terkait_data_lainya,
            'reporter_id' => $this->pelapor_pengguna_id,
            'notification_status' => $this->status_notifikasi,
            'verification_status' => $this->status_verifikasi,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public static function fromUnifiedFormat($data)
    {
        return [
            'nama_team_pelapor' => $data['team_name'] ?? '',
            'jumlah_personel' => $data['personnel_count'] ?? 0,
            'informasi_singkat_bencana' => $data['brief_info'] ?? '',
            'lokasi_bencana' => $data['location'] ?? '',
            'foto_lokasi_bencana' => $data['photo'] ?? '',
            'titik_kordinat_lokasi_bencana' => $data['coordinates'] ?? '',
            'skala_bencana' => $data['scale'] ?? '',
            'jumlah_korban' => $data['casualties'] ?? 0,
            'bukti_surat_perintah_tugas' => $data['task_document'] ?? '',
            'deskripsi_terkait_data_lainya' => $data['additional_info'] ?? '',
            'pelapor_pengguna_id' => $data['reporter_id'] ?? null,
            'status_notifikasi' => $data['notification_status'] ?? 0,
            'status_verifikasi' => $data['verification_status'] ?? 0,
        ];
    }
}
