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
        'status',
        'permintaan_pengembalian'
    ];

    protected function casts(): array
    {
        return [
            'tgl_pinjam' => 'date:Y-m-d',
            'tgl_kembali_plan' => 'date:Y-m-d',
            'permintaan_pengembalian' => 'boolean',
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
        return $this->hasOne(Pengembalian::class, 'peminjaman_id');
    }

    // Menentukan tampilan badge berdasarkan status peminjaman
   
public function statusBadge(): array
{
    return match ($this->status) {

        'diajukan' => [
            'label' => 'Menunggu Persetujuan',
            'color' => 'bg-yellow-100 text-yellow-700',
        ],

        'dipinjamkan' => [
            'label' => 'Sedang Dipinjam',
            'color' => 'bg-blue-100 text-blue-700',
        ],

        'telat' => [
            'label' => 'Terlambat',
            'color' => 'bg-red-100 text-red-700',
        ],

        'dikembalikan' => [
            'label' => 'Sudah Dikembalikan',
            'color' => 'bg-gray-100 text-gray-700',
        ],

        'ditolak' => [
            'label' => 'Ditolak',
            'color' => 'bg-red-100 text-red-700',
        ],

        default => [
            'label' => ucfirst($this->status ?? 'Tidak Diketahui'),
            'color' => 'bg-gray-100 text-gray-700',
        ],
    };
}
    public function getStatusTampilAttribute(): string
    {
        if ($this->status === 'dipinjamkan'
            && $this->tgl_kembali_plan
            && $this->tgl_kembali_plan->lt(today())) {
            return 'telat';
        }

        return $this->status;
    }


}