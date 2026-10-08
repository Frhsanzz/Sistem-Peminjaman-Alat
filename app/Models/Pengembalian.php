<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\Carbon;

class Pengembalian extends Model
{
    protected $table = 'pengembalian';

    protected $fillable = [
    'peminjaman_id',
    'tgl_kembali',
    'kondisi_kembali',
    'denda',
    'denda_terlambat',
    'denda_kerusakan',
    'catatan_kerusakan',
    'petugas_id',
];

    protected function casts(): array {
        return [
            'tgl_kembali' => 'date:Y-m-d',
            'denda' => 'integer',
        ];
    }

    public function peminjaman(): BelongsTo {
        return $this->belongsTo(Peminjaman::class);
    } 

    public function petugas(): BelongsTo {
        return $this->belongsTo(User::class, 'petugas_id');
    }
    

const DENDA_TERLAMBAT = 5000;

public function details()
{
    return $this->hasMany(PengembalianDetail::class);
}



public function getTotalRusakAttribute(): int
{
    return (int) $this->details->sum('jumlah_rusak');
}
    }
