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
    /**
     * Dashboard Kasir
     * Menampilkan statistik harian dan jadwal aktif
     */
    public function dashboard()
    {
        $today = now()->toDateString();

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

        // Ambil jadwal hari ini
        $jadwals = Jadwal::with(['film', 'studio'])
            ->whereDate('tanggal_tayang', $today)
            ->orderBy('jam_tayang')
            ->get();

        // Hitung harga final (weekend vs weekday)
        $jadwals->map(function($jadwal) {
            $tanggalTayang = \Carbon\Carbon::parse($jadwal->tanggal_tayang);
            $isWeekend = $tanggalTayang->isWeekend();
            
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

    /**
     * Halaman Check-in
     */
    public function checkInPage()
    {
        return view('kasir.checkin');
    }

    /**
     * Check tiket by booking code
     */
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

    /**
     * Scan QR Code tiket
     */
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

    /**
     * Gunakan tiket (check-in)
     */
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

    /**
     * Print tiket
     */
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

    /**
     * Halaman Jual Tiket Offline
     */
    public function jualTiketPage()
    {
        $today = now()->toDateString();

        $jadwals = Jadwal::with(['film', 'studio'])
            ->whereDate('tanggal_tayang', $today)
            ->orderBy('jam_tayang')
            ->get();

        // Hitung harga final
        $jadwals->map(function ($jadwal) {
            $tanggalTayang = \Carbon\Carbon::parse($jadwal->tanggal_tayang);
            $isWeekend = $tanggalTayang->isWeekend();
            $jadwal->harga_final = $isWeekend ? 45000 : $jadwal->harga_dasar;
            $jadwal->is_weekend = $isWeekend;
            return $jadwal;
        });

        return view('kasir.jual-tiket', compact('jadwals'));
    }

    /**
     * Get kursi tersedia untuk jadwal tertentu
     */
    public function getKursiTersedia($jadwalId)
    {
        $jadwal = Jadwal::with('studio')->findOrFail($jadwalId);
        
        // Ambil semua kursi di studio
        $allKursis = Kursi::where('studio_id', $jadwal->studio_id)
            ->where('status_aktif', true)
            ->orderBy('baris')
            ->orderBy('kolom')
            ->get();

        // Kursi yang sudah dipesan
        $bookedKursiIds = DB::table('pemesanan_kursis')
            ->join('pemesanans', 'pemesanan_kursis.pemesanan_id', '=', 'pemesanans.id')
            ->where('pemesanans.jadwal_id', $jadwalId)
            ->whereIn('pemesanans.status_pembayaran', ['sudah_bayar', 'pending'])
            ->whereIn('pemesanans.status_pemesanan', ['dikonfirmasi', 'digunakan', 'pending'])
            ->pluck('pemesanan_kursis.kursi_id')
            ->toArray();

        // Hitung harga berdasarkan weekend/weekday
        $tanggalTayang = \Carbon\Carbon::parse($jadwal->tanggal_tayang);
        $isWeekend = $tanggalTayang->isWeekend();
        $hargaFinal = $isWeekend ? 45000 : $jadwal->harga_dasar;

        $kursis = $allKursis->map(function($kursi) use ($bookedKursiIds, $hargaFinal) {
            return [
                'id' => $kursi->id,
                'nomor_kursi' => $kursi->nomor_kursi,
                'baris' => $kursi->baris,
                'kolom' => $kursi->kolom,
                'harga' => $hargaFinal,
                'status' => in_array($kursi->id, $bookedKursiIds) ? 'booked' : 'available'
            ];
        });

        return response()->json([
            'success' => true,
            'kursis' => $kursis,
            'jadwal' => $jadwal,
            'harga_per_kursi' => $hargaFinal
        ]);
    }

    /**
     * Store tiket offline (CASH)
     */
    public function storeTiketOffline(Request $request)
{
    $request->validate([
        'jadwal_id' => 'required|exists:jadwals,id',
        'kursi_ids' => 'required|array|min:1',
        'kursi_ids.*' => 'exists:kursis,id',
        'metode_bayar' => 'required|in:tunai',
        'total_bayar' => 'required|numeric|min:0'
        // ✅ HAPUS: nama_pelanggan, email_pelanggan, no_hp_pelanggan
    ]);

        DB::beginTransaction();
        try {
            // Cek kursi sudah dipesan atau belum
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

            $jadwal = Jadwal::findOrFail($request->jadwal_id);
            
            // Hitung harga
            $tanggalTayang = \Carbon\Carbon::parse($jadwal->tanggal_tayang);
            $isWeekend = $tanggalTayang->isWeekend();
            $hargaPerKursi = $isWeekend ? 45000 : $jadwal->harga_dasar;
            
            $jumlahKursi = count($request->kursi_ids);
            $totalHarga = $jumlahKursi * $hargaPerKursi;

            // Generate kode booking
            $today = date('Ymd');
            $count = Pemesanan::whereDate('created_at', today())->count();
            $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            $kodeBooking = "OFFLINE-{$today}-{$sequence}";

            // Create pemesanan
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

            // Create pemesanan_kursi
            foreach ($request->kursi_ids as $kursiId) {
                PemesananKursi::create([
                    'pemesanan_id' => $pemesanan->id,
                    'kursi_id' => $kursiId,
                    'harga_kursi' => $hargaPerKursi,
                ]);
            }

            // Create pembayaran
        Pembayaran::create([
            'pemesanan_id' => $pemesanan->id,
            'jumlah_bayar' => $totalHarga,
            'metode_pembayaran' => 'tunai',
            'status_pembayaran' => 'berhasil',
            'tanggal_bayar' => now(),
            'diproses_oleh' => auth()->id(),
            'detail_pembayaran' => json_encode([
                'tipe' => 'offline_cash',
                'metode_bayar' => 'tunai',
                'kasir' => auth()->user()->nama_lengkap ?? auth()->user()->name,
                'waktu_transaksi' => now()->toDateTimeString(),
            ])
        ]);

            // Update kursi tersedia
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
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tiket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Snap Token untuk pembayaran digital offline
     */
    public function generateSnapTokenOffline(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwals,id',
            'kursi_ids' => 'required|array|min:1',
            'nama_pelanggan' => 'required|string',
            'email_pelanggan' => 'nullable|email',
            'no_hp_pelanggan' => 'required|string',
            'total_bayar' => 'required|numeric'
        ]);

        DB::beginTransaction();
        try {
            // Cek kursi
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
                    'message' => 'Kursi sudah dipesan orang lain.'
                ], 400);
            }

            $jadwal = Jadwal::findOrFail($request->jadwal_id);
            
            $tanggalTayang = \Carbon\Carbon::parse($jadwal->tanggal_tayang);
            $isWeekend = $tanggalTayang->isWeekend();
            $hargaPerKursi = $isWeekend ? 45000 : $jadwal->harga_dasar;
            
            $jumlahKursi = count($request->kursi_ids);
            $totalHarga = $jumlahKursi * $hargaPerKursi;

            // Generate kode booking
            $today = date('Ymd');
            $count = Pemesanan::whereDate('created_at', today())->count();
            $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            $kodeBooking = "OFFLINE-{$today}-{$sequence}";

            // Create pemesanan (status pending dulu)
            $pemesanan = Pemesanan::create([
                'user_id' => auth()->id(),
                'jadwal_id' => $request->jadwal_id,
                'kode_pemesanan' => $kodeBooking,
                'jumlah_kursi' => $jumlahKursi,
                'total_bayar' => $totalHarga,
                'status_pembayaran' => 'pending',
                'status_pemesanan' => 'pending',
                'tipe_pemesanan' => 'offline',
                'diproses_oleh' => auth()->id(),
                'tanggal_pesan' => now(),
            ]);

            // Create pemesanan_kursi
            foreach ($request->kursi_ids as $kursiId) {
                PemesananKursi::create([
                    'pemesanan_id' => $pemesanan->id,
                    'kursi_id' => $kursiId,
                    'harga_kursi' => $hargaPerKursi,
                ]);
            }

            // Generate Snap Token
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            $params = [
                'transaction_details' => [
                    'order_id' => $kodeBooking,
                    'gross_amount' => $totalHarga,
                ],
                'customer_details' => [
                    'first_name' => $request->nama_pelanggan,
                    'email' => $request->email_pelanggan ?? 'offline@happycine.com',
                    'phone' => $request->no_hp_pelanggan,
                ],
                'item_details' => [
                    [
                        'id' => $jadwal->id,
                        'price' => $hargaPerKursi,
                        'quantity' => $jumlahKursi,
                        'name' => $jadwal->film->judul . ' - ' . $jadwal->studio->nama_studio,
                    ]
                ],
                'enabled_payments' => [
                    'gopay', 'shopeepay', 'other_qris', 
                    'bca_va', 'bni_va', 'bri_va', 'permata_va',
                    'echannel', 'other_va'
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Create pembayaran
            Pembayaran::create([
                'pemesanan_id' => $pemesanan->id,
                'jumlah_bayar' => $totalHarga,
                'metode_pembayaran' => 'e_wallet', // Default dulu
                'status_pembayaran' => 'pending',
                'diproses_oleh' => auth()->id(),
                'detail_pembayaran' => json_encode([
                    'tipe' => 'offline_digital',
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'email_pelanggan' => $request->email_pelanggan,
                    'no_hp_pelanggan' => $request->no_hp_pelanggan,
                    'kasir' => auth()->user()->nama_lengkap,
                    'snap_token' => $snapToken,
                ])
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $kodeBooking,
                'pemesanan_id' => $pemesanan->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Generate snap token offline error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate payment token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check payment status untuk pembayaran digital offline
     */
    public function checkPaymentStatusOffline($orderId)
    {
        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');

            /** @var object $status */
            // Cek status ke Midtrans
            $status = \Midtrans\Transaction::status($orderId);
            
            $isPaid = in_array($status->transaction_status, ['capture', 'settlement']);
            
            if ($isPaid) {
                $pemesanan = Pemesanan::where('kode_pemesanan', $orderId)->first();
                
                if ($pemesanan && $pemesanan->status_pembayaran !== 'sudah_bayar') {
                    // Update pemesanan
                    $pemesanan->update([
                        'status_pembayaran' => 'sudah_bayar',
                        'status_pemesanan' => 'dikonfirmasi',
                        'tanggal_bayar' => now(),
                    ]);

                    // Update pembayaran
                    $pembayaran = Pembayaran::where('pemesanan_id', $pemesanan->id)->first();
                    if ($pembayaran) {
                        // Detect metode pembayaran dari Midtrans
                        $metodePembayaran = $this->detectPaymentMethod($status->payment_type);
                        
                        $pembayaran->update([
                            'status_pembayaran' => 'berhasil',
                            'metode_pembayaran' => $metodePembayaran,
                            'tanggal_bayar' => now(),
                            'detail_pembayaran' => json_encode([
                                'tipe' => 'offline_digital',
                                'payment_type' => $status->payment_type,
                                'transaction_id' => $status->transaction_id,
                                'transaction_time' => $status->transaction_time,
                                'settlement_time' => $status->settlement_time ?? null,
                            ])
                        ]);
                    }

                    // Update kursi tersedia
                    $jadwal = Jadwal::find($pemesanan->jadwal_id);
                    if ($jadwal) {
                        $jadwal->decrement('kursi_tersedia', $pemesanan->jumlah_kursi);
                    }
                }

                return response()->json([
                    'success' => true,
                    'is_paid' => true,
                    'status_pembayaran' => 'sudah_bayar',
                    'print_url' => route('kasir.print.tiket', $pemesanan->id)
                ]);
            }

            return response()->json([
                'success' => true,
                'is_paid' => false,
                'status_pembayaran' => 'pending',
                'midtrans_status' => $status->transaction_status
            ]);

        } catch (\Exception $e) {
            Log::error('Check payment status offline error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'is_paid' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detect payment method dari Midtrans payment_type
     */
    private function detectPaymentMethod($paymentType)
    {
        $mapping = [
            'gopay' => 'e_wallet',
            'shopeepay' => 'e_wallet',
            'qris' => 'e_wallet',
            'bank_transfer' => 'transfer_bank',
            'echannel' => 'transfer_bank',
            'bca_va' => 'transfer_bank',
            'bni_va' => 'transfer_bank',
            'bri_va' => 'transfer_bank',
            'permata_va' => 'transfer_bank',
            'other_va' => 'transfer_bank',
            'credit_card' => 'kartu_kredit',
        ];

        return $mapping[$paymentType] ?? 'e_wallet';
    }

    /**
     * Halaman Verifikasi Pembayaran
     */
    public function verifikasiPembayaran()
    {
        $pembayarans = Pembayaran::with(['pemesanan.jadwal.film', 'pemesanan.jadwal.studio', 'pemesanan.user', 'pemesanan.kursi'])
            ->where('status_pembayaran', 'pending')
            ->whereNotNull('bukti_transfer')
            ->latest()
            ->paginate(10);
        
        return view('kasir.verifikasi-pembayaran', compact('pembayarans'));
    }

    /**
     * Konfirmasi Pembayaran
     */
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

    /**
     * Tolak Pembayaran
     */
    public function tolakPembayaran(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        
        $pembayaran->update([
            'status_pembayaran' => 'gagal',
            'catatan_penolakan' => $request->alasan,
        ]);
        
        $pembayaran->pemesanan->update([
            'status_pembayaran' => 'belum_bayar',
        ]);
        
        return redirect()->back()->with('success', '❌ Pembayaran ditolak!');
    }
}