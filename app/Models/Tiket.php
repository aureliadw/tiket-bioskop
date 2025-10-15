<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    use HasFactory;

    protected $fillable = [
        'pemesanan_kursi_id',
        'kode_tiket',
        'qr_code',
        'status_tiket',
        'tanggal_cetak',
        'tanggal_pakai',
        'divalidasi_oleh',
    ];

    protected $casts = [
        'tanggal_cetak' => 'datetime',
        'tanggal_pakai' => 'datetime',
    ];

    public function pemesananKursi()
    {
        return $this->belongsTo(PemesananKursi::class);
    }
}
