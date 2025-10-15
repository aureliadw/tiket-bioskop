<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemesanan;

class TiketController extends Controller
{
    public function verify(Request $request, $code)
    {
        $signature = $request->query('sig');
        
        if ($signature) {
            $validSignature = substr(hash_hmac('sha256', $code, config('app.key')), 0, 16);
            
            if ($signature !== $validSignature) {
                return view('pelanggan.verify', [  // ← GANTI DI SINI
                    'valid' => false,
                    'message' => 'QR Code tidak valid atau sudah dimodifikasi'
                ]);
            }
        }
        
        $pemesanan = Pemesanan::with([
            'jadwal.film', 
            'jadwal.studio', 
            'kursi', 
            'user'
        ])
        ->where('kode_pemesanan', $code)
        ->first();
        
        if (!$pemesanan) {
            return view('pelanggan.verify', [  // ← GANTI DI SINI
                'valid' => false,
                'message' => 'Booking tidak ditemukan'
            ]);
        }
        
        if ($pemesanan->status_pembayaran !== 'sudah_bayar') {
            return view('pelanggan.verify', [  // ← GANTI DI SINI
                'valid' => false,
                'message' => 'Tiket belum dibayar',
                'pemesanan' => $pemesanan
            ]);
        }
        
        if ($pemesanan->status_pemesanan === 'digunakan') {
            return view('pelanggan.verify', [  // ← GANTI DI SINI
                'valid' => false,
                'message' => 'Tiket sudah digunakan pada ' . $pemesanan->used_at->format('d M Y, H:i'),
                'pemesanan' => $pemesanan
            ]);
        }
        
        $isKasir = auth()->check() && in_array(auth()->user()->role, ['kasir', 'admin']);
        
        return view('pelanggan.verify', [  // ← GANTI DI SINI
            'valid' => true,
            'pemesanan' => $pemesanan,
            'message' => 'Tiket Valid!',
            'isKasir' => $isKasir
        ]);
    }
}
