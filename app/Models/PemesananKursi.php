<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemesananKursi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pemesanan_id',
        'kursi_id',
        'harga_kursi',
    ];

    protected $casts = [
        'harga_kursi' => 'decimal:2',
    ];

    // Relationships
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function kursi()
    {
        return $this->belongsTo(Kursi::class);
    }

    public function tiket()
    {
        return $this->hasOne(Tiket::class);
    }
}
