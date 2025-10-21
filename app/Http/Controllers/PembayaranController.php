<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\PemesananKursi;
use App\Models\Kursi;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PembayaranController extends Controller
{
    /**
     * ============================================
     * SHOW - Tampilkan Halaman Pembayaran
     * ============================================
     * 
     * Fitur:
     * 1. Load data pemesanan dengan relasi
     * 2. Hitung harga berdasarkan weekend/weekday
     * 3. Tampilkan form pembayaran
     * 
     * @param int $pemesanan_id
     * @return \Illuminate\View\View
     */
    public function show($pemesanan_id)
    {
        // CEK LOGIN
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // AMBIL DATA PEMESANAN
        $pemesanan = Pemesanan::with(['jadwal.film', 'jadwal.studio', 'kursi'])
            ->where('user_id', Auth::id())
            ->findOrFail($pemesanan_id);

        $kursis = $pemesanan->kursi;

        // HITUNG HARGA (Weekend vs Weekday)
        $hargaDasar = $pemesanan->jadwal->harga_dasar ?? 35000;
        $tanggalTayang = Carbon::parse($pemesanan->jadwal->tanggal_tayang);
        $isWeekend = $tanggalTayang->isWeekend();
        $hargaFinal = $isWeekend ? 45000 : $hargaDasar;

        // KALKULASI TOTAL
        $subtotal = $hargaFinal * $kursis->count();
        $diskon = 0;
        $total = $subtotal - $diskon;

        $user = Auth::user();

        return view('pelanggan.pembayaran', compact(
            'pemesanan',
            'kursis',
            'subtotal',
            'diskon',
            'total',
            'user'
        ));
    }

    /**
     * ============================================
     * PROSES PEMBAYARAN - Simpan Data Pembayaran
     * ============================================
     * 
     * Flow:
     * 1. Validasi input
     * 2. Generate kode booking (jika belum ada)
     * 3. Hitung ulang total bayar
     * 4. Simpan/Update pembayaran
     * 5. Update status pemesanan
     * 
     * @param Request $request
     * @param int $pemesanan_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function prosesPembayaran(Request $request, $pemesanan_id)
    {
        // CEK LOGIN
        if (!Auth::check()) {
            return response()->json([
                'success' => false, 
                'message' => 'Unauthorized'
            ], 401);
        }

        // VALIDASI INPUT
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'metode_pembayaran' => 'required|in:dana,gopay,ovo,bca,mandiri',
        ]);

        // AMBIL DATA PEMESANAN
        $pemesanan = Pemesanan::where('user_id', Auth::id())
            ->findOrFail($pemesanan_id);

        // HITUNG ULANG TOTAL BAYAR
        $kursis = $pemesanan->kursi;
        $hargaDasar = $pemesanan->jadwal->harga_dasar ?? 35000;
        $tanggalTayang = Carbon::parse($pemesanan->jadwal->tanggal_tayang);
        $isWeekend = $tanggalTayang->isWeekend();
        $hargaFinal = $isWeekend ? 45000 : $hargaDasar;

        $subtotal = $hargaFinal * $kursis->count();
        $biaya_admin = 3500;
        $total_bayar = $subtotal + $biaya_admin;

        // GENERATE KODE BOOKING (jika belum ada)
        if (empty($pemesanan->kode_pemesanan)) {
            $today = date('Ymd'); // Format: 20251019
            
            // Hitung pemesanan hari ini
            $count = Pemesanan::whereDate('created_at', today())->count();
            
            // Format: TIX-YYYYMMDD-XXXX
            $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            $kodeBooking = "TIX-{$today}-{$sequence}";
            
            $pemesanan->kode_pemesanan = $kodeBooking;
        }

        // MAPPING METODE PEMBAYARAN
        $metodeMap = [
            'dana' => 'e_wallet',
            'gopay' => 'e_wallet',
            'ovo' => 'e_wallet',
            'bca' => 'transfer_bank',
            'mandiri' => 'transfer_bank',
        ];

        // SIMPAN/UPDATE PEMBAYARAN
        $pembayaran = Pembayaran::updateOrCreate(
            ['pemesanan_id' => $pemesanan->id],
            [
                'jumlah_bayar' => $total_bayar,
                'metode_pembayaran' => $metodeMap[$request->metode_pembayaran],
                'status_pembayaran' => 'pending',
                'tanggal_bayar' => now(),
            ]
        );

        // UPDATE PEMESANAN
        $pemesanan->update([
            'total_bayar' => $total_bayar,
            'status_pembayaran' => 'belum_bayar',
            'status_pemesanan' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data pembayaran berhasil disimpan',
            'booking_code' => $pemesanan->kode_pemesanan
        ]);
    }

    /**
     * ============================================
     * UPLOAD BUKTI - Upload Bukti Transfer
     * ============================================
     * 
     * Fitur:
     * 1. Validasi file (image, max 2MB)
     * 2. Simpan file ke storage
     * 3. Update status pembayaran jadi "pending"
     * 4. Menunggu verifikasi admin
     * 
     * @param Request $request
     * @param int $pemesanan_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadBukti(Request $request, $pemesanan_id)
    {
        // CEK LOGIN
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // VALIDASI INPUT
        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'catatan' => 'nullable|string|max:500',
        ]);

        // AMBIL DATA PEMESANAN & PEMBAYARAN
        $pemesanan = Pemesanan::where('user_id', Auth::id())
            ->findOrFail($pemesanan_id);

        $pembayaran = Pembayaran::where('pemesanan_id', $pemesanan->id)
            ->firstOrFail();

        // UPLOAD FILE
        if ($request->hasFile('bukti_transfer')) {
            $file = $request->file('bukti_transfer');
            $filename = 'bukti_' . $pemesanan_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('bukti_transfer', $filename, 'public');

            // UPDATE PEMBAYARAN
            $pembayaran->update([
                'bukti_transfer' => $path,
                'detail_pembayaran' => json_encode(['catatan' => $request->catatan]),
                'status_pembayaran' => 'pending', // Menunggu verifikasi admin
            ]);

            // UPDATE PEMESANAN
            $pemesanan->update([
                'status_pemesanan' => 'pending',
                'status_pembayaran' => 'belum_bayar',
            ]);
        }

        return redirect()->route('profile.index')
            ->with('success', 'Bukti transfer berhasil diupload. Menunggu verifikasi admin (1-24 jam).');
    }

    /**
     * ============================================
     * SHOW TIKET - Tampilkan E-Ticket
     * ============================================
     * 
     * Fitur:
     * 1. Load data pemesanan
     * 2. Generate QR Code dengan signature
     * 3. Tampilkan tiket digital
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showTiket($id)
    {
        // AMBIL DATA PEMESANAN
        $pemesanan = Pemesanan::with(['jadwal.film', 'jadwal.studio', 'kursi', 'user'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // CEK STATUS PEMBAYARAN
        if ($pemesanan->status_pembayaran !== 'sudah_bayar') {
            // Tampilkan tiket tapi tanpa QR Code
            return view('pelanggan.tiket', compact('pemesanan'));
        }

        // GENERATE QR CODE dengan SIGNATURE (Security)
        $bookingCode = $pemesanan->kode_pemesanan;
        
        // Generate signature untuk keamanan
        $secretKey = config('app.qr_secret_key', 'default-secret-key');
        $timestamp = time();
        $signature = hash_hmac('sha256', $bookingCode . '|' . $timestamp, $secretKey);
        $shortSig = substr($signature, 0, 16); // Ambil 16 karakter pertama
        
        // Generate URL untuk QR Code
        $verifyUrl = route('tiket.verify', [
            'code' => $bookingCode,
            'sig' => $shortSig,
            't' => $timestamp
        ]);

        return view('pelanggan.tiket', compact('pemesanan', 'bookingCode', 'verifyUrl'));
    }
}
