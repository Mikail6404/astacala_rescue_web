<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Pengguna Model - Web App User/Volunteer Authentication
 * 
 * This model provides backward compatibility for the web app's user system
 * while maintaining integration with the unified backend authentication system.
 * 
 * NOTE: This is a transitional model. Future versions should migrate to
 * unified User model with role-based authentication.
 */
class Pengguna extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     */
    protected $table = 'penggunas';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'username_akun_pengguna',
        'password_akun_pengguna',
        'nama_lengkap_pengguna',
        'tanggal_lahir_pengguna',
        'tempat_lahir_pengguna',
        'no_handphone_pengguna',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password_akun_pengguna',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'password_akun_pengguna' => 'hashed',
        ];
    }

    /**
     * Get the password attribute name for authentication.
     */
    public function getAuthPassword()
    {
        return $this->password_akun_pengguna;
    }

    /**
     * Get the username attribute name for authentication.
     */
    public function getAuthIdentifierName()
    {
        return 'username_akun_pengguna';
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
            'name' => $this->nama_lengkap_pengguna,
            'email' => $this->username_akun_pengguna, // Using username as email equivalent
            'role' => 'VOLUNTEER',
            'phone' => $this->no_handphone_pengguna,
            'organization' => 'Community Volunteer',
            'birth_date' => $this->tanggal_lahir_pengguna,
            'is_active' => true,
        ];
    }

    /**
     * Get user profile data.
     */
    public function getProfileData(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username_akun_pengguna,
            'nama_lengkap' => $this->nama_lengkap_pengguna,
            'tanggal_lahir' => $this->tanggal_lahir_pengguna,
            'tempat_lahir' => $this->tempat_lahir_pengguna,
            'no_handphone' => $this->no_handphone_pengguna,
            'role' => 'volunteer',
        ];
    }

    /**
     * Create API token for mobile app integration.
     */
    public function createApiToken(string $name = 'mobile-token'): string
    {
        return $this->createToken($name)->plainTextToken;
    }
}
