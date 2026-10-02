<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'no_hp',
        'alamat',
        'foto_profil',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_active_at' => 'datetime',
        ];
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class);
    }

    public function scopeTersedia($query)
    {
        return $query->where('stok', '>', 0)
                     ->where('status_kondisi', 'Baik');
    }

    public function getIsOnlineAttribute()
    {
        return $this->last_active_at
            && $this->last_active_at->gt(now()->subMinutes(5));
    }

    public function getSedangMeminjamAttribute()
    {
        return $this->peminjaman()
            ->whereIn('status', [
                'diajukan',
                'dipinjamkan',
                'telat',
            ])
            ->exists();
    }
}