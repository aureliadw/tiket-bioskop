<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $fillable = [
        'film_id',
        'studio_id',
        'tanggal_tayang',
        'jam_tayang',
        'harga_dasar',
        'kursi_tersedia',
        'status_aktif',
    ];

    protected $casts = [
        'tanggal_tayang' => 'date',
        'jam_tayang' => 'datetime:H:i',
        'harga_dasar' => 'decimal:2',
        'kursi_tersedia' => 'integer',
        'status_aktif' => 'boolean',
    ];

    public function film()
    {
        return $this->belongsTo(Film::class);
    }

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class);
    }
}
