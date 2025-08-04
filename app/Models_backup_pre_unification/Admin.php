<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admins'; // nama tabel di database

    protected $fillable = [
        'username_akun_admin',
        'password_akun_admin',
        'nama_lengkap_admin',
        'tanggal_lahir_admin',
        'tempat_lahir_admin',
        'no_anggota',
        'no_handphone_admin',
        // Tambahkan kolom lain jika ada
    ];

    protected $hidden = [
        "password_akun_admin",
    ];
}
