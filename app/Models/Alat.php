<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alat extends Model
{
    protected $table = 'alat';

    protected $fillable = [
        'kategori_id', 'nama_alat', 'stok', 'jumlah_rusak', 'status_kondisi',
        'deskripsi', 'gambar', 'keterangan_rusak', 'tanggal_rusak',
    ];

    // Dua deklarasi casts di model lama digabung jadi satu
    protected function casts(): array
    {
        return [
            'stok'          => 'integer',
            'jumlah_rusak'  => 'integer',
            'tanggal_rusak' => 'date',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function detailPinjam(): HasMany
    {
        return $this->hasMany(DetailPinjam::class);
    }

    // Stok yang masih baik = stok - jumlah_rusak  (dipakai di view: $item->stok_baik)
    public function getStokBaikAttribute(): int
    {
        return max(0, (int) $this->stok - (int) $this->jumlah_rusak);
    }

    // Mengisi status_kondisi otomatis dari stok dan jumlah_rusak
    public function syncKondisi(): void
    {
        if ($this->jumlah_rusak <= 0) {
            $this->status_kondisi = 'baik';
        } elseif ($this->jumlah_rusak >= $this->stok) {
            $this->status_kondisi = 'rusak';
        } else {
            $this->status_kondisi = 'sebagian_rusak';
        }
        $this->save();
    }
    public function scopeRusak($query)
{
    return $query->where('jumlah_rusak', '>', 0);
}

// [teks label, kelas css] untuk badge
public function getKondisiLabelAttribute(): array
{
    return match (true) {
        $this->jumlah_rusak <= 0            => ['Baik', 'badge-baik'],
        $this->jumlah_rusak >= $this->stok  => ['Rusak semua', 'badge-rusak'],
        default                             => ['Sebagian rusak', 'badge-sebagian'],
    };
}
}