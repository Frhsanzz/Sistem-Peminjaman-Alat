<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'user_id',
        'tgl_pinjam',
        'tgl_kembali_plan',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'tgl_pinjam' => 'date:Y-m-d',
            'tgl_kembali_plan' => 'date:Y-m-d',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detailPinjam(): HasMany
    {
        return $this->hasMany(DetailPinjam::class);
    }

    public function pengembalian(): HasOne
    {
        return $this->hasOne(Pengembalian::class);
    }

    // Menentukan tampilan badge berdasarkan status peminjaman
    public function statusBadge(): array
    {
        return match ($this->status) {

            'diajukan' => [
                'label' => 'Menunggu Persetujuan',
                'color' => 'bg-yellow-100 text-yellow-700',
            ],

            'disetujui' => [
                'label' => 'Disetujui',
                'color' => 'bg-green-100 text-green-700',
            ],

            'ditolak' => [
                'label' => 'Ditolak',
                'color' => 'bg-red-100 text-red-700',
            ],

            'dipinjam' => [
                'label' => 'Sedang Dipinjam',
                'color' => 'bg-blue-100 text-blue-700',
            ],

            'dikembalikan' => [
                'label' => 'Sudah Dikembalikan',
                'color' => 'bg-gray-100 text-gray-700',
            ],

            default => [
                'label' => ucfirst($this->status ?? 'Tidak Diketahui'),
                'color' => 'bg-gray-100 text-gray-700',
            ],
        };
    }
}