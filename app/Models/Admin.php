<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Admin Model - Web App Administrator Authentication
 *
 * This model provides backward compatibility for the web app's admin system
 * while maintaining integration with the unified backend authentication system.
 *
 * NOTE: This is a transitional model. Future versions should migrate to
 * unified User model with role-based authentication.
 */
class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     */
    protected $table = 'admins';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'username_akun_admin',
        'password_akun_admin',
        'nama_lengkap_admin',
        'tanggal_lahir_admin',
        'tempat_lahir_admin',
        'no_anggota',
        'no_handphone_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password_akun_admin',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'password_akun_admin' => 'hashed',
        ];
    }

    /**
     * Get the password attribute name for authentication.
     */
    public function getAuthPassword()
    {
        return $this->password_akun_admin;
    }

    /**
     * Get the username attribute name for authentication.
     */
    public function getAuthIdentifierName()
    {
        return 'username_akun_admin';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Convert to unified User format for backend API integration.
     * This method allows seamless integration with the unified backend system.
     */
    public function toUnifiedUser(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->nama_lengkap_admin,
            'email' => $this->username_akun_admin, // Using username as email equivalent
            'role' => 'ADMIN',
            'phone' => $this->no_handphone_admin,
            'organization' => 'Astacala Rescue Team',
            'birth_date' => $this->tanggal_lahir_admin,
            'is_active' => true,
        ];
    }

    /**
     * Get admin profile data.
     */
    public function getProfileData(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username_akun_admin,
            'nama_lengkap' => $this->nama_lengkap_admin,
            'tanggal_lahir' => $this->tanggal_lahir_admin,
            'tempat_lahir' => $this->tempat_lahir_admin,
            'no_anggota' => $this->no_anggota,
            'no_handphone' => $this->no_handphone_admin,
            'role' => 'admin',
        ];
    }
}
