<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jadwal_id',
        'kode_pemesanan',
        'jumlah_kursi',
        'total_bayar',
        'status_pemesanan',
        'status_pembayaran',
        'tipe_pemesanan',
        'diproses_oleh',
        'tanggal_pesan',
        'tanggal_bayar',
        'used_at',   
        'used_by',
    ];

    protected $casts = [
        'total_bayar' => 'decimal:2',
        'jumlah_kursi' => 'integer',
        'tanggal_pesan' => 'datetime',
        'tanggal_bayar' => 'datetime',
        'used_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pemesanan) {
            // Generate kode booking jika belum ada
            if (empty($pemesanan->kode_pemesanan)) {
                $today = date('Ymd'); // 20250114
                
                // Hitung pemesanan hari ini
                $count = static::whereDate('created_at', today())->count();
                
                // Format: TIX-YYYYMMDD-XXXX
                $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
                
                $pemesanan->kode_pemesanan = "TIX-{$today}-{$sequence}";
            }
        });
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    // Relasi ke pivot table
    public function pemesananKursi()
    {
        return $this->hasMany(PemesananKursi::class, 'pemesanan_id');
    }

    // Relasi langsung ke kursi
    public function kursi()
    {
        return $this->belongsToMany(Kursi::class, 'pemesanan_kursis', 'pemesanan_id', 'kursi_id')
                    ->withPivot('harga_kursi')
                    ->withTimestamps();
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'pemesanan_id');
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
