<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengembalianDetail extends Model
{
    protected $table = 'pengembalian_detail';

    protected $fillable = ['pengembalian_id', 'alat_id', 'jumlah_dipinjam', 'jumlah_rusak'];

    public function alat()
    {
        return $this->belongsTo(Alat::class);
    }
}