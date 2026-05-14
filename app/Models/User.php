<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Memeriksa apakah user memiliki setidaknya satu role di dalam scope tertentu.
     * 
     * @param string|array $scopes (contoh: 'manajemen' atau ['manajemen', 'pimpinan'])
     * @return bool
     */
    public function hasRoleScope($scopes): bool
    {
        $scopes = is_array($scopes) ? $scopes : func_get_args();
        
        // Ambil semua role yang dimiliki user saat ini
        $userRoles = $this->roles;

        // Cek apakah ada role yang memiliki scope yang dicari
        return $userRoles->whereIn('scope', $scopes)->isNotEmpty();
    }
}
