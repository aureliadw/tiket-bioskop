<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'pemesanan_id',
        'jumlah_bayar',
        'metode_pembayaran',
        'id_transaksi',
        'status_pembayaran',
        'detail_pembayaran',
        'bukti_transfer',
        'tanggal_bayar',
        'diproses_oleh',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'detail_pembayaran' => 'array',
        'tanggal_bayar' => 'datetime',
    ];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'pemesanan_id');
    }
}
