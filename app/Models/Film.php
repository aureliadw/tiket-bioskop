<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'deskripsi',
        'genre',
        'durasi',
        'sutradara',
        'produser',
        'produksi',
        'penulis',
        'pemain',
        'poster_image',
        'trailer_video',
        'rating',
        'status',
        'tanggal_rilis',
    ];

    protected $casts = [
        'tanggal_rilis' => 'date',
        'rating' => 'decimal:1',
        'durasi' => 'integer',
    ];

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}
