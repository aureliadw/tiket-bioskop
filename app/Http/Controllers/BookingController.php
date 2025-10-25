<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Film;
use App\Models\Jadwal;
use App\Models\Kursi;
use App\Models\Pemesanan;
use App\Models\PemesananKursi;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

/**
 * ====================================================================
 * BOOKING CONTROLLER - Handle Pemesanan Tiket Online
 * ====================================================================
 * 
 * Flow:
 * 1. User pilih kursi (pilihKursi)
 * 2. User klik "Lanjut Pembayaran" (prosesKursi) - create pemesanan
 * 3. Generate Snap Token Midtrans (generateSnapToken)
 * 4. User bayar via Midtrans popup
 * 5. Polling check status (checkPaymentStatus) - auto update status
 * 6. Redirect ke riwayat tiket
 */
class BookingController extends Controller
{
    public function __construct()
    {
        // Setup Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * ====================================================================
     * PILIH KURSI - Tampilkan halaman pemilihan kursi
     * ====================================================================
     * 
     * Fitur:
     * - Auto-hapus pemesanan expired (belum bayar > 10 menit)
     * - Tampilkan kursi yang tersedia, terjual, dan pending
     * - Hitung harga weekend vs weekday
     */
    public function pilihKursi($jadwalId)
    {
        // 1. AUTO-RELEASE: Hapus pemesanan yang sudah lewat 10 menit tapi belum bayar
        Pemesanan::where('status_pembayaran', 'belum_bayar')
            ->where('created_at', '<', now()->subMinutes(10))
            ->delete();

        // 2. Ambil data jadwal (film, studio)
        $jadwal = Jadwal::with(['film', 'studio'])->findOrFail($jadwalId);
        
        // 3. Ambil semua kursi di studio ini
        $kursis = Kursi::where('studio_id', $jadwal->studio_id)
            ->orderBy('baris')
            ->orderBy('kolom')
            ->get();

        // 4. Kursi TERJUAL (sudah_bayar) - tidak bisa dipilih
        $kursiTerjual = DB::table('pemesanan_kursis')
            ->join('pemesanans', 'pemesanan_kursis.pemesanan_id', '=', 'pemesanans.id')
            ->where('pemesanans.jadwal_id', $jadwalId)
            ->where('pemesanans.status_pembayaran', 'sudah_bayar')
            ->pluck('pemesanan_kursis.kursi_id')
            ->toArray();

        // 5. Kursi PENDING (belum bayar < 10 menit) - sedang di-hold user lain
        $kursiPending = DB::table('pemesanan_kursis')
            ->join('pemesanans', 'pemesanan_kursis.pemesanan_id', '=', 'pemesanans.id')
            ->where('pemesanans.jadwal_id', $jadwalId)
            ->where('pemesanans.status_pembayaran', 'belum_bayar')
            ->where('pemesanans.created_at', '>=', now()->subMinutes(10))
            ->pluck('pemesanan_kursis.kursi_id')
            ->toArray();

        return view('pelanggan.pilih-kursi', compact('jadwal', 'kursis', 'kursiTerjual', 'kursiPending'));
    }

    /**
     * ====================================================================
     * PROSES KURSI - Buat pemesanan baru (called via AJAX)
     * ====================================================================
     * 
     * Input: Array kursi_ids
     * Output: JSON {success, pemesanan_id, kode_pemesanan}
     * 
     * Fitur:
     * - Validasi kursi belum dipesan
     * - Generate kode booking unik
     * - Hitung harga (weekend +10rb)
     * - Save pemesanan + kursi ke database
     */
    public function prosesKursi(Request $request, $jadwal_id)
    {
        // 1. Validasi user sudah login
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
        }

        // 2. Validasi input kursi
        $request->validate([
            'kursi' => 'required|array|min:1',
            'kursi.*' => 'integer|exists:kursis,id',
        ]);

        $jadwal = Jadwal::findOrFail($jadwal_id);

        // 3. Hitung harga (weekend = Rp45.000, weekday = harga_dasar)
        $tanggalTayang = Carbon::parse($jadwal->tanggal_tayang);
        $isWeekend = $tanggalTayang->isWeekend(); // true jika Sabtu/Minggu
        $hargaFinal = $isWeekend ? 45000 : $jadwal->harga_dasar;

        DB::beginTransaction();
        
        try {
            // 4. CEK CONFLICT: Apakah kursi sudah dipesan?
            $kursiTerjual = DB::table('pemesanan_kursis')
                ->join('pemesanans', 'pemesanan_kursis.pemesanan_id', '=', 'pemesanans.id')
                ->where('pemesanans.jadwal_id', $jadwal_id)
                ->whereIn('pemesanans.status_pembayaran', ['sudah_bayar', 'belum_bayar'])
                ->where(function($q) {
                    $q->where('pemesanans.status_pembayaran', 'sudah_bayar')
                      ->orWhere(function($q2) {
                          $q2->where('pemesanans.status_pembayaran', 'belum_bayar')
                             ->where('pemesanans.created_at', '>=', now()->subMinutes(10));
                      });
                })
                ->pluck('pemesanan_kursis.kursi_id')
                ->toArray();

            $conflict = array_intersect($request->kursi, $kursiTerjual);
            if (!empty($conflict)) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Kursi sudah dipesan. Refresh halaman.'], 400);
            }

            // 5. GENERATE KODE BOOKING UNIK (HPC-20251024-A3F8D2)
            $today = date('Ymd');
            $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
            $kodePemesanan = "HPC-{$today}-{$random}";

            // Pastikan benar-benar unique
            while (Pemesanan::where('kode_pemesanan', $kodePemesanan)->exists()) {
                $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
                $kodePemesanan = "HPC-{$today}-{$random}";
            }

            // 6. HITUNG TOTAL (harga tiket + biaya admin Rp3.500)
            $subtotal = count($request->kursi) * $hargaFinal;
            $biayaAdmin = 3500;
            $totalBayar = $subtotal + $biayaAdmin;

            // 7. BUAT PEMESANAN
            $pemesanan = Pemesanan::create([
                'user_id'           => Auth::id(),
                'jadwal_id'         => $jadwal->id,
                'kode_pemesanan'    => $kodePemesanan,
                'jumlah_kursi'      => count($request->kursi),
                'total_bayar'       => $totalBayar,
                'status_pemesanan'  => 'pending',
                'status_pembayaran' => 'belum_bayar',
                'tipe_pemesanan'    => 'online',
                'tanggal_pesan'     => now(),
            ]);

            // 8. SIMPAN DETAIL KURSI
            foreach ($request->kursi as $kursiId) {
                DB::table('pemesanan_kursis')->insert([
                    'pemesanan_id' => $pemesanan->id,
                    'kursi_id'     => $kursiId,
                    'harga_kursi'  => $hargaFinal,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pemesanan berhasil dibuat',
                'pemesanan_id' => $pemesanan->id,
                'kode_pemesanan' => $kodePemesanan,
            ]);
            
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Proses kursi gagal: ' . $e->getMessage());
            
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * ====================================================================
     * GENERATE SNAP TOKEN - Request token dari Midtrans (called via AJAX)
     * ====================================================================
     * 
     * Output: JSON {success, snap_token, order_id}
     * 
     * Token ini digunakan untuk membuka popup pembayaran Midtrans
     */
    public function generateSnapToken($pemesanan_id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $pemesanan = Pemesanan::with(['jadwal.film', 'kursi'])
            ->where('user_id', Auth::id())
            ->findOrFail($pemesanan_id);

        $user = Auth::user();

        try {
            // 1. SETUP DATA UNTUK MIDTRANS
            $params = [
                'transaction_details' => [
                    'order_id' => $pemesanan->kode_pemesanan,
                    'gross_amount' => (int) $pemesanan->total_bayar,
                ],
                'customer_details' => [
                    'first_name' => $user->nama_lengkap,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '08123456789',
                ],
                'item_details' => [
                    [
                        'id' => $pemesanan->jadwal->film->id,
                        'price' => (int) ($pemesanan->total_bayar - 3500) / $pemesanan->jumlah_kursi,
                        'quantity' => $pemesanan->jumlah_kursi,
                        'name' => $pemesanan->jadwal->film->judul,
                    ], 
                    [
                        'id' => 'admin_fee',
                        'price' => 3500,
                        'quantity' => 1,
                        'name' => 'Biaya Admin',
                    ]
                ],
                'enabled_payments' => [
                    'qris', 'gopay', 'shopeepay', 'other_qris', 
                    'bank_transfer', 'bca_va', 'bni_va', 'bri_va', 'permata_va'
                ],
            ];

            // 2. REQUEST SNAP TOKEN DARI MIDTRANS
            $snapToken = Snap::getSnapToken($params);

            // 3. SIMPAN TOKEN KE DATABASE
            Pembayaran::updateOrCreate(
                ['pemesanan_id' => $pemesanan->id],
                [
                    'jumlah_bayar' => $pemesanan->total_bayar,
                    'metode_pembayaran' => 'midtrans',
                    'status_pembayaran' => 'pending',
                    'detail_pembayaran' => json_encode([
                        'snap_token' => $snapToken,
                        'order_id' => $pemesanan->kode_pemesanan,
                        'payment_gateway' => 'midtrans',
                    ]),
                ]
            );

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $pemesanan->kode_pemesanan,
            ]);

        } catch (\Exception $e) {
            Log::error('Generate Snap Token Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal generate pembayaran: ' . $e->getMessage()], 500);
        }
    }

    /**
     * ====================================================================
     * CHECK PAYMENT STATUS - Cek apakah user sudah bayar (Polling via AJAX)
     * ====================================================================
     * 
     * Fitur:
     * - Cek status langsung ke Midtrans API
     * - AUTO-UPDATE status jika sudah bayar
     * 
     * Called setiap 3 detik dari JavaScript
     */
    

    

    public function checkPaymentStatus($orderId)
{
    try {
        /** @var object $status */
        $status = \Midtrans\Transaction::status($orderId);
        
        $isPaid = in_array($status->transaction_status, ['capture', 'settlement']);
        
        if ($isPaid) {
            $pemesanan = Pemesanan::where('kode_pemesanan', $orderId)
                ->where('user_id', Auth::id())
                ->first();
            
            if ($pemesanan && $pemesanan->status_pembayaran !== 'sudah_bayar') {
                $pemesanan->update([
                    'status_pembayaran' => 'sudah_bayar',
                    'status_pemesanan' => 'dikonfirmasi',
                    'tanggal_bayar' => now(),
                ]);
                
                $pembayaran = Pembayaran::where('pemesanan_id', $pemesanan->id)->first();
                if ($pembayaran) {
                    // ✅ MAPPING: Ubah payment_type Midtrans ke format database
                    $paymentType = $status->payment_type ?? 'e_wallet';
                    
                    // Mapping logic
                    if ($paymentType == 'bank_transfer' || $paymentType == 'echannel') {
                        $metodePembayaran = 'transfer_bank';
                    } elseif (in_array($paymentType, ['gopay', 'shopeepay', 'qris', 'other_qris'])) {
                        $metodePembayaran = 'e_wallet';
                    } elseif ($paymentType == 'credit_card') {
                        $metodePembayaran = 'kartu_kredit';
                    } else {
                        $metodePembayaran = 'e_wallet'; // default
                    }
                    
                    $pembayaran->update([
                        'status_pembayaran' => 'berhasil',
                        'tanggal_bayar' => now(),
                        'metode_pembayaran' => $metodePembayaran, // ✅ SIMPAN HASIL MAPPING
                    ]);
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'status_pembayaran' => $isPaid ? 'sudah_bayar' : 'belum_bayar',
            'is_paid' => $isPaid,
        ]);
        
    } catch (\Exception $e) {
        Log::error('Check Payment Error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
    /**
     * ====================================================================
     * MIDTRANS WEBHOOK - Terima notifikasi dari server Midtrans
     * ====================================================================
     * 
     * TIDAK DIPAKAI untuk localhost (karena Midtrans tidak bisa akses localhost)
     * Hanya jalan kalau sudah production dengan domain public
     * 
     * Fungsi: Backup auto-update status kalau polling gagal
     */
    public function midtransWebhook(Request $request)
    {
        try {
            $notification = new \Midtrans\Notification();

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;

            Log::info('Midtrans Webhook: ' . json_encode([
                'order_id' => $orderId,
                'status' => $transactionStatus,
                'fraud' => $fraudStatus,
            ]));

            $pemesanan = Pemesanan::where('kode_pemesanan', $orderId)->first();
            if (!$pemesanan) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            $pembayaran = Pembayaran::where('pemesanan_id', $pemesanan->id)->first();

            // Update status berdasarkan notifikasi
if ($transactionStatus == 'capture' && $fraudStatus == 'accept' || $transactionStatus == 'settlement') {
    $pemesanan->update([
        'status_pembayaran' => 'sudah_bayar',
        'status_pemesanan' => 'dikonfirmasi',
        'tanggal_bayar' => now(),
    ]);

    if ($pembayaran) {
        $pembayaran->update([
            'status_pembayaran' => 'berhasil',
            'tanggal_bayar' => now(),
            'metode_pembayaran' => $notification->payment_type ?? 'unknown', // ✅ TAMBAH INI
        ]);
    }
} else if ($transactionStatus == 'pending') {
    $pemesanan->update(['status_pembayaran' => 'belum_bayar']);
    if ($pembayaran) $pembayaran->update(['status_pembayaran' => 'pending']);
} else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
    $pemesanan->update(['status_pembayaran' => 'gagal', 'status_pemesanan' => 'dibatalkan']);
    if ($pembayaran) $pembayaran->update(['status_pembayaran' => 'gagal']);
}
            return response()->json(['message' => 'Webhook processed']);

        } catch (\Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
     * ====================================================================
     * SHOW TIKET - Tampilkan e-ticket setelah pembayaran sukses
     * ====================================================================
     */
    public function showTiket($id)
    {
        $pemesanan = Pemesanan::with(['jadwal.film', 'jadwal.studio', 'kursi', 'user'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($pemesanan->status_pembayaran !== 'sudah_bayar') {
            return view('pelanggan.tiket', compact('pemesanan'));
        }

        $bookingCode = $pemesanan->kode_pemesanan;
        
        // Generate QR code URL dengan signature
        $secretKey = config('app.qr_secret_key', 'default-secret-key');
        $timestamp = time();
        $signature = hash_hmac('sha256', $bookingCode . '|' . $timestamp, $secretKey);
        $shortSig = substr($signature, 0, 16);
        
        $verifyUrl = route('tiket.verify', [
            'code' => $bookingCode,
            'sig' => $shortSig,
            't' => $timestamp
        ]);

        return view('pelanggan.tiket', compact('pemesanan', 'bookingCode', 'verifyUrl'));
    }
}
