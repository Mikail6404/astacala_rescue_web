<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeritaBencana extends Model
{
    use HasFactory;

    protected $table = 'beritabencana';

    protected $fillable = [
        'pblk_judul_bencana',
        'deskripsi_umum',
        'pblk_titik_kordinat_bencana',
        'pblk_lokasi_bencana',
        'pblk_skala_bencana',
        'dibuat_oleh_admin_id',
        'pblk_foto_bencana',
        'is_published'
    ];
}
