<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemesanan;
use App\Models\Jadwal;
use App\Models\Pembayaran;
use App\Models\PemesananKursi;
use App\Models\Kursi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KasirController extends Controller
{
public function dashboard()
{
    $today = now()->toDateString();
    $now = now();

    $stats = [
        'total_checkin' => Pemesanan::whereDate('used_at', $today)
            ->where('status_pemesanan', 'digunakan')
            ->count(),

        'total_penjualan' => Pemesanan::whereDate('created_at', $today)
            ->where('status_pembayaran', 'sudah_bayar')
            ->sum('total_bayar'),

        'tiket_pending' => 0,

        'jadwal_hari_ini' => Jadwal::whereDate('tanggal_tayang', $today)->count()
    ];

    // ✅ Ambil SEMUA jadwal hari ini
    $jadwals = Jadwal::with(['film', 'studio'])
        ->whereDate('tanggal_tayang', $today)
        ->orderBy('jam_tayang')
        ->get();

    // ✅ HITUNG HARGA FINAL (Weekend vs Weekday) untuk setiap jadwal
    $jadwals->map(function($jadwal) {
        $tanggalTayang = \Carbon\Carbon::parse($jadwal->tanggal_tayang);
        $isWeekend = $tanggalTayang->isWeekend(); // Sabtu/Minggu?
        
        // Jika weekend → Rp 45.000, jika weekday → pakai harga_dasar
        $jadwal->harga_final = $isWeekend ? 45000 : $jadwal->harga_dasar;
        $jadwal->is_weekend = $isWeekend;
        
        return $jadwal;
    });

    $recentCheckins = Pemesanan::with(['jadwal.film', 'user', 'usedBy'])
        ->where('status_pemesanan', 'digunakan')
        ->whereNotNull('used_at')
        ->whereDate('used_at', $today)
        ->orderBy('used_at', 'desc')
        ->limit(10)
        ->get();

    return view('kasir.dashboard', compact('stats', 'jadwals', 'recentCheckins'));
}

    // ========== CHECK-IN ==========
    public function checkInPage()
    {
        return view('kasir.checkin');
    }

    public function checkByCode(Request $request)
    {
        $request->validate([
            'booking_code' => 'required|string'
        ]);

        $pemesanan = Pemesanan::with([
            'jadwal.film', 
            'jadwal.studio', 
            'kursi', 
            'user'
        ])
        ->where('kode_pemesanan', $request->booking_code)
        ->first();

        if (!$pemesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }

        if ($pemesanan->status_pembayaran !== 'sudah_bayar') {
            return response()->json([
                'success' => false,
                'message' => 'Tiket belum dibayar',
                'pemesanan' => $pemesanan
            ], 400);
        }

        if ($pemesanan->status_pemesanan === 'digunakan') {
            return response()->json([
                'success' => false,
                'message' => 'Tiket sudah digunakan pada ' . $pemesanan->used_at->format('d M Y, H:i'),
                'pemesanan' => $pemesanan
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tiket Valid!',
            'pemesanan' => $pemesanan
        ]);
    }

    public function scanTiket(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string'
        ]);

        $url = $request->qr_data;
        preg_match('/\/tiket\/verify\/([A-Z0-9\-]+)/', $url, $matches);
        
        if (!isset($matches[1])) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid'
            ], 400);
        }

        $bookingCode = $matches[1];
        $request->merge(['booking_code' => $bookingCode]);
        return $this->checkByCode($request);
    }

    public function useTiket($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);

        if ($pemesanan->status_pemesanan === 'digunakan') {
            return response()->json([
                'success' => false,
                'message' => 'Tiket sudah digunakan!'
            ], 400);
        }

        $pemesanan->update([
            'status_pemesanan' => 'digunakan',
            'used_at' => now(),
            'used_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil! Tiket siap dicetak.',
            'print_url' => route('kasir.print.tiket', $pemesanan->id)
        ]);
    }

    // ========== PRINT TIKET ==========
    public function printTiket($id)
    {
        $pemesanan = Pemesanan::with([
            'jadwal.film',
            'jadwal.studio',
            'kursi',
            'user'
        ])->findOrFail($id);

        return view('kasir.print-tiket', compact('pemesanan'));
    }

    // ========== JUAL TIKET OFFLINE ==========
public function jualTiketPage()
{
    $now = now();
    $today = $now->toDateString();

    // ✅ Ambil SEMUA jadwal hari ini
    $jadwals = Jadwal::with(['film', 'studio'])
        ->whereDate('tanggal_tayang', $today)
        ->orderBy('jam_tayang')
        ->get();

    // Tambahkan informasi weekend dan harga_final
    $jadwals->map(function ($jadwal) {
        $tanggalTayang = \Carbon\Carbon::parse($jadwal->tanggal_tayang);
        $isWeekend = $tanggalTayang->isWeekend();
        $jadwal->harga_final = $isWeekend ? 45000 : $jadwal->harga_dasar;
        $jadwal->is_weekend = $isWeekend;
        return $jadwal;
    });

    return view('kasir.jual-tiket', compact('jadwals'));
}


public function getKursiTersedia($jadwalId)
{
    $jadwal = Jadwal::with('studio')->findOrFail($jadwalId);
    
    // Ambil semua kursi di studio ini
    $allKursis = Kursi::where('studio_id', $jadwal->studio_id)
        ->where('status_aktif', true)
        ->orderBy('baris')
        ->orderBy('kolom')
        ->get();

    // Ambil kursi yang sudah dipesan
    $bookedKursiIds = DB::table('pemesanan_kursis')
        ->join('pemesanans', 'pemesanan_kursis.pemesanan_id', '=', 'pemesanans.id')
        ->where('pemesanans.jadwal_id', $jadwalId)
        ->whereIn('pemesanans.status_pembayaran', ['sudah_bayar', 'pending']) // Tambah pending
        ->whereIn('pemesanans.status_pemesanan', ['dikonfirmasi', 'digunakan', 'pending'])
        ->pluck('pemesanan_kursis.kursi_id')
        ->toArray();

    $kursis = $allKursis->map(function($kursi) use ($bookedKursiIds, $jadwal) {
        return [
            'id' => $kursi->id,
            'nomor_kursi' => $kursi->nomor_kursi,
            'baris' => $kursi->baris,
            'kolom' => $kursi->kolom,
            'harga' => $jadwal->harga_dasar, // Pakai harga dari jadwal
            'status' => in_array($kursi->id, $bookedKursiIds) ? 'booked' : 'available'
        ];
    });

    return response()->json([
        'success' => true,
        'kursis' => $kursis,
        'jadwal' => $jadwal,
        'harga_per_kursi' => $jadwal->harga_dasar
    ]);
}

public function storeTiketOffline(Request $request)
{
    $request->validate([
        'jadwal_id' => 'required|exists:jadwals,id',
        'kursi_ids' => 'required|array|min:1',
        'kursi_ids.*' => 'exists:kursis,id',
        'nama_pelanggan' => 'required|string|max:255',
        'email_pelanggan' => 'nullable|email',
        'no_hp_pelanggan' => 'required|string|max:20',
        'metode_bayar' => 'required|in:tunai,transfer_bank,e_wallet,kartu_kredit' // ✅ FIX: Sesuaikan dengan ENUM
    ]);

    DB::beginTransaction();
    try {
        // ===== CEK KURSI SUDAH DIPESAN ATAU BELUM =====
        $bookedKursiIds = DB::table('pemesanan_kursis')
            ->join('pemesanans', 'pemesanan_kursis.pemesanan_id', '=', 'pemesanans.id')
            ->where('pemesanans.jadwal_id', $request->jadwal_id)
            ->whereIn('pemesanans.status_pembayaran', ['sudah_bayar', 'pending'])
            ->whereIn('pemesanans.status_pemesanan', ['dikonfirmasi', 'digunakan', 'pending'])
            ->pluck('pemesanan_kursis.kursi_id')
            ->toArray();

        $conflict = array_intersect($request->kursi_ids, $bookedKursiIds);
        if (!empty($conflict)) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Kursi sudah dipesan orang lain. Silakan refresh halaman.'
            ], 400);
        }

        // ===== AMBIL DATA JADWAL =====
        $jadwal = Jadwal::findOrFail($request->jadwal_id);
        
        // ===== HITUNG TOTAL HARGA =====
        $jumlahKursi = count($request->kursi_ids);
        $hargaPerKursi = $jadwal->harga_dasar;
        $totalHarga = $jumlahKursi * $hargaPerKursi;

        // ===== GENERATE KODE BOOKING =====
        $today = date('Ymd');
        $count = Pemesanan::whereDate('created_at', today())->count();
        $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        $kodeBooking = "OFFLINE-{$today}-{$sequence}";

        // ===== CREATE PEMESANAN =====
        $pemesanan = Pemesanan::create([
            'user_id' => auth()->id(),
            'jadwal_id' => $request->jadwal_id,
            'kode_pemesanan' => $kodeBooking,
            'jumlah_kursi' => $jumlahKursi,
            'total_bayar' => $totalHarga,
            'status_pembayaran' => 'sudah_bayar',
            'status_pemesanan' => 'dikonfirmasi',
            'tipe_pemesanan' => 'offline',
            'diproses_oleh' => auth()->id(),
            'tanggal_pesan' => now(),
            'tanggal_bayar' => now(),
        ]);

        // ===== CREATE PEMESANAN_KURSI =====
        foreach ($request->kursi_ids as $kursiId) {
            PemesananKursi::create([
                'pemesanan_id' => $pemesanan->id,
                'kursi_id' => $kursiId,
                'harga_kursi' => $hargaPerKursi,
            ]);
        }

        // ===== CREATE PEMBAYARAN =====
        Pembayaran::create([
            'pemesanan_id' => $pemesanan->id,
            'jumlah_bayar' => $totalHarga,
            'metode_pembayaran' => $request->metode_bayar, // ✅ Sekarang sudah sesuai ENUM
            'status_pembayaran' => 'berhasil',
            'tanggal_bayar' => now(),
            'diproses_oleh' => auth()->id(),
            'detail_pembayaran' => json_encode([
                'tipe' => 'offline',
                'nama_pelanggan' => $request->nama_pelanggan,
                'email_pelanggan' => $request->email_pelanggan,
                'no_hp_pelanggan' => $request->no_hp_pelanggan,
                'metode_bayar' => $request->metode_bayar,
                'kasir' => auth()->user()->nama_lengkap,
            ])
        ]);

        // ===== UPDATE KURSI TERSEDIA =====
        $jadwal->decrement('kursi_tersedia', $jumlahKursi);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Tiket offline berhasil dibuat!',
            'pemesanan_id' => $pemesanan->id,
            'kode_pemesanan' => $kodeBooking,
            'total_bayar' => $totalHarga,
            'print_url' => route('kasir.print.tiket', $pemesanan->id)
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Store tiket offline error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal membuat tiket: ' . $e->getMessage()
        ], 500);
    }
}

    // ✅ TAMBAH METHOD INI

// Halaman Verifikasi Pembayaran
public function verifikasiPembayaran()
{
    $pembayarans = Pembayaran::with(['pemesanan.jadwal.film', 'pemesanan.jadwal.studio', 'pemesanan.user', 'pemesanan.kursi'])
        ->where('status_pembayaran', 'pending')
        ->whereNotNull('bukti_transfer')
        ->latest()
        ->paginate(10);
    
    return view('kasir.verifikasi-pembayaran', compact('pembayarans'));
}

// Konfirmasi Pembayaran
public function konfirmasiPembayaran($id)
{
    $pembayaran = Pembayaran::findOrFail($id);
    
    $pembayaran->update([
        'status_pembayaran' => 'berhasil',
        'tanggal_bayar' => now(),
        'diproses_oleh' => auth()->id(), // ID Kasir yang konfirmasi
    ]);
    
    $pembayaran->pemesanan->update([
        'status_pembayaran' => 'sudah_bayar',
        'status_pemesanan' => 'dikonfirmasi',
    ]);
    
    return redirect()->back()->with('success', '✅ Pembayaran berhasil dikonfirmasi!');
}

// Tolak Pembayaran
public function tolakPembayaran(Request $request, $id)
{
    $pembayaran = Pembayaran::findOrFail($id);
    
    $pembayaran->update([
        'status_pembayaran' => 'gagal',
        'catatan_penolakan' => $request->alasan, // Simpan alasan
    ]);
    
    $pembayaran->pemesanan->update([
        'status_pembayaran' => 'belum_bayar',
    ]);
    
    return redirect()->back()->with('success', '❌ Pembayaran ditolak!');
}
}
