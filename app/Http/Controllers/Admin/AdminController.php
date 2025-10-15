<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\User;
use App\Models\Film;
use carbon\Carbon;

class AdminController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        $today = Carbon::today();
        
        $stats = [
            'total_pemesanan' => Pemesanan::count(),
            'pemesanan_hari_ini' => Pemesanan::whereDate('created_at', $today)->count(),
            'pending_pembayaran' => Pembayaran::where('status_pembayaran', 'pending')
                                            ->whereNotNull('bukti_transfer')
                                            ->count(),
            'total_revenue' => Pembayaran::where('status_pembayaran', 'berhasil')
                                        ->sum('jumlah_bayar'),
            'total_film' => Film::count(),
            'total_pelanggan' => User::where('role', 'user')->count(),
        ];
        
        $recentOrders = Pemesanan::with(['jadwal.film', 'user', 'pembayaran'])
                                ->latest()
                                ->take(5)
                                ->get();
        
        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }

    // List Pembayaran Pending (Untuk Verifikasi)
    public function pembayaran()
    {
        $pembayarans = Pembayaran::with(['pemesanan.jadwal.film', 'pemesanan.jadwal.studio', 'pemesanan.user'])
            ->where('status_pembayaran', 'pending')
            ->whereNotNull('bukti_transfer')
            ->latest()
            ->paginate(10);
        
        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    // Konfirmasi Pembayaran
    public function konfirmasiPembayaran($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        
        $pembayaran->update([
            'status_pembayaran' => 'berhasil',
            'tanggal_bayar' => now(),
            'diproses_oleh' => auth()->id(),
        ]);
        
        $pembayaran->pemesanan->update([
            'status_pembayaran' => 'sudah_bayar',
            'status_pemesanan' => 'dikonfirmasi',
        ]);
        
        return redirect()->back()->with('success', '✅ Pembayaran berhasil dikonfirmasi!');
    }

    // Tolak Pembayaran
    public function tolakPembayaran($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        
        $pembayaran->update([
            'status_pembayaran' => 'gagal',
        ]);
        
        $pembayaran->pemesanan->update([
            'status_pembayaran' => 'belum_bayar',
        ]);
        
        return redirect()->back()->with('error', '❌ Pembayaran ditolak!');
    }

    // ✅ LIST SEMUA PEMESANAN (ALL ORDERS)
    public function pemesanan(Request $request)
    {
        $query = Pemesanan::with(['jadwal.film', 'jadwal.studio', 'user', 'pembayaran']);

        // Filter by status pembayaran
        if ($request->has('status') && $request->status != '') {
            $query->whereHas('pembayaran', function($q) use ($request) {
                $q->where('status_pembayaran', $request->status);
            });
        }

        // Search by kode pemesanan atau nama user
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_pemesanan', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        $pemesanans = $query->latest()->paginate(15);
        
        return view('admin.pemesanan.index', compact('pemesanans'));
    }

    // ✅ DETAIL PEMESANAN
    public function pemesananDetail($id)
    {
        $pemesanan = Pemesanan::with([
            'jadwal.film', 
            'jadwal.studio', 
            'user', 
            'pembayaran',
            'kursi' 
        ])->findOrFail($id);
        
        return view('admin.pemesanan.detail', compact('pemesanan'));
    }
}
