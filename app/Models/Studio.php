<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Studio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_studio',
        'total_kursi',
        'deskripsi',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'total_kursi' => 'integer',
    ];

    public function kursis()
    {
        return $this->hasMany(Kursi::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }
}
