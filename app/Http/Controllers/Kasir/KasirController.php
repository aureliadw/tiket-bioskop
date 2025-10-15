<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemesanan;
use App\Models\Jadwal;
use App\Models\Kursi;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    // ========== DASHBOARD ==========
    public function dashboard()
    {
        $today = now()->format('Y-m-d');
        
        // Statistik hari ini
        $stats = [
            'total_checkin' => Pemesanan::whereDate('used_at', $today)->count(),
            'total_penjualan' => Pemesanan::whereDate('created_at', $today)
                ->where('status_pembayaran', 'sudah_bayar')
                ->sum('total_bayar'),
            'tiket_pending' => Pemesanan::where('status_pembayaran', 'sudah_bayar')
                ->where('status_pemesanan', 'aktif')
                ->whereHas('jadwal', function($q) use ($today) {
                    $q->whereDate('tanggal_tayang', $today);
                })
                ->count(),
            'jadwal_hari_ini' => Jadwal::whereDate('tanggal_tayang', $today)->count()
        ];

        // Jadwal hari ini
        $jadwals = Jadwal::with(['film', 'studio'])
            ->whereDate('tanggal_tayang', $today)
            ->orderBy('jam_tayang')
            ->get();

        // Recent check-ins
        $recentCheckins = Pemesanan::with(['jadwal.film', 'user', 'usedBy'])
            ->where('status_pemesanan', 'digunakan')
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
        $today = now()->format('Y-m-d');
        
        $jadwals = Jadwal::with(['film', 'studio'])
            ->whereDate('tanggal_tayang', '>=', $today)
            ->orderBy('tanggal_tayang')
            ->orderBy('jam_tayang')
            ->get();

        return view('kasir.jual-tiket', compact('jadwals'));
    }

    public function getKursiTersedia($jadwalId)
    {
        $jadwal = Jadwal::with('studio')->findOrFail($jadwalId);
        
        // Ambil semua kursi di studio ini
        $allKursis = Kursi::where('studio_id', $jadwal->studio_id)
            ->orderBy('nomor_kursi')
            ->get();

        // Ambil kursi yang sudah dipesan
        $bookedKursiIds = DB::table('pemesanan_kursi')
            ->join('pemesanans', 'pemesanan_kursi.pemesanan_id', '=', 'pemesanans.id')
            ->where('pemesanans.jadwal_id', $jadwalId)
            ->where('pemesanans.status_pembayaran', 'sudah_bayar')
            ->pluck('pemesanan_kursi.kursi_id')
            ->toArray();

        $kursis = $allKursis->map(function($kursi) use ($bookedKursiIds) {
            return [
                'id' => $kursi->id,
                'nomor_kursi' => $kursi->nomor_kursi,
                'tipe' => $kursi->tipe,
                'harga' => $kursi->harga,
                'status' => in_array($kursi->id, $bookedKursiIds) ? 'booked' : 'available'
            ];
        });

        return response()->json([
            'success' => true,
            'kursis' => $kursis,
            'jadwal' => $jadwal
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
            'metode_bayar' => 'required|in:cash,debit,qris'
        ]);

        DB::beginTransaction();
        try {
            // Cek kursi masih available
            $bookedKursiIds = DB::table('pemesanan_kursi')
                ->join('pemesanans', 'pemesanan_kursi.pemesanan_id', '=', 'pemesanans.id')
                ->where('pemesanans.jadwal_id', $request->jadwal_id)
                ->where('pemesanans.status_pembayaran', 'sudah_bayar')
                ->pluck('pemesanan_kursi.kursi_id')
                ->toArray();

            $conflict = array_intersect($request->kursi_ids, $bookedKursiIds);
            if (!empty($conflict)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kursi sudah dipesan orang lain. Refresh halaman.'
                ], 400);
            }

            // Hitung total harga
            $kursis = Kursi::whereIn('id', $request->kursi_ids)->get();
            $totalHarga = $kursis->sum('harga');

            // Generate kode booking
            $kodeBooking = 'OFFLINE-' . date('Ymd') . '-' . str_pad(Pemesanan::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Buat pemesanan (user_id = kasir yang login)
            $pemesanan = Pemesanan::create([
                'user_id' => auth()->id(), // Kasir
                'jadwal_id' => $request->jadwal_id,
                'kode_pemesanan' => $kodeBooking,
                'total_harga' => $totalHarga,
                'status_pembayaran' => 'sudah_bayar',
                'status_pemesanan' => 'digunakan', // Langsung digunakan (offline)
                'used_at' => now(),
                'used_by' => auth()->id(),
                'metadata' => json_encode([
                    'tipe' => 'offline',
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'email_pelanggan' => $request->email_pelanggan,
                    'no_hp_pelanggan' => $request->no_hp_pelanggan,
                    'metode_bayar' => $request->metode_bayar
                ])
            ]);

            // Attach kursi
            $pemesanan->kursi()->attach($request->kursi_ids);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tiket offline berhasil dibuat!',
                'pemesanan_id' => $pemesanan->id,
                'print_url' => route('kasir.print.tiket', $pemesanan->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tiket: ' . $e->getMessage()
            ], 500);
        }
    }
}
