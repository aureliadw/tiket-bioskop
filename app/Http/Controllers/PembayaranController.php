<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\PemesananKursi;
use App\Models\Kursi;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function show($pemesanan_id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $pemesanan = Pemesanan::with(['jadwal.film', 'jadwal.studio', 'kursi'])
            ->where('user_id', Auth::id())
            ->findOrFail($pemesanan_id);

        $kursis = $pemesanan->kursi;

        // Ambil harga dasar dari jadwal
        $hargaDasar = $pemesanan->jadwal->harga_dasar ?? 35000;

        // Tentukan apakah tanggal tayang adalah weekend
        $tanggalTayang = \Carbon\Carbon::parse($pemesanan->jadwal->tanggal_tayang);
        $isWeekend = $tanggalTayang->isWeekend();

        // Hitung harga akhir per tiket
        $hargaFinal = $isWeekend ? 45000 : $hargaDasar;

        // Kalikan dengan jumlah kursi yang dipesan
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

    // ✅ METHOD BARU - FIXED: Generate Kode Booking
    public function prosesPembayaran(Request $request, $pemesanan_id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'metode_pembayaran' => 'required|in:dana,gopay,ovo,bca,mandiri',
        ]);

        $pemesanan = Pemesanan::where('user_id', Auth::id())
            ->findOrFail($pemesanan_id);

        // 🧮 Hitung ulang total bayar
        $kursis = $pemesanan->kursi;
        $hargaDasar = $pemesanan->jadwal->harga_dasar ?? 35000;

        $tanggalTayang = \Carbon\Carbon::parse($pemesanan->jadwal->tanggal_tayang);
        $isWeekend = $tanggalTayang->isWeekend();
        $hargaFinal = $isWeekend ? 45000 : $hargaDasar;

        $subtotal = $hargaFinal * $kursis->count();
        $biaya_admin = 3500;
        $total_bayar = $subtotal + $biaya_admin;

        // ✅ GENERATE KODE BOOKING (Jika belum ada)
        if (empty($pemesanan->kode_pemesanan)) {
            $today = date('Ymd'); // 20250114
            
            // Hitung pemesanan hari ini
            $count = Pemesanan::whereDate('created_at', today())->count();
            
            // Format: TIX-YYYYMMDD-XXXX
            $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            $kodeBooking = "TIX-{$today}-{$sequence}";
            
            $pemesanan->kode_pemesanan = $kodeBooking;
        }

        // Mapping metode pembayaran
        $metodeMap = [
            'dana' => 'e_wallet',
            'gopay' => 'e_wallet',
            'ovo' => 'e_wallet',
            'bca' => 'transfer_bank',
            'mandiri' => 'transfer_bank',
        ];

        // 💾 Simpan/Update pembayaran
        $pembayaran = Pembayaran::updateOrCreate(
            ['pemesanan_id' => $pemesanan->id],
            [
                'jumlah_bayar' => $total_bayar,
                'metode_pembayaran' => $metodeMap[$request->metode_pembayaran],
                'status_pembayaran' => 'pending',
                'tanggal_bayar' => now(),
            ]
        );

        // 🔁 Update pemesanan
        $pemesanan->update([
            'total_bayar' => $total_bayar,
            'status_pembayaran' => 'belum_bayar',
            'status_pemesanan' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data pembayaran berhasil disimpan',
            'booking_code' => $pemesanan->kode_pemesanan // ← Kirim ke frontend
        ]);
    }

    public function uploadBukti(Request $request, $pemesanan_id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'catatan' => 'nullable|string|max:500',
        ]);

        $pemesanan = Pemesanan::where('user_id', Auth::id())
            ->findOrFail($pemesanan_id);

        $pembayaran = Pembayaran::where('pemesanan_id', $pemesanan->id)
            ->firstOrFail();

        if ($request->hasFile('bukti_transfer')) {
            $file = $request->file('bukti_transfer');
            $filename = 'bukti_' . $pemesanan_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('bukti_transfer', $filename, 'public');

            $pembayaran->update([
                'bukti_transfer' => $path,
                'detail_pembayaran' => json_encode(['catatan' => $request->catatan]),
                'status_pembayaran' => 'pending',
            ]);

            $pemesanan->update([
                'status_pemesanan' => 'pending',
                'status_pembayaran' => 'belum_bayar',
            ]);
        }

        return redirect()->route('profile.index')
            ->with('success', 'Bukti transfer berhasil diupload. Menunggu verifikasi admin (1-24 jam).');
    }

    // ✅ HALAMAN TIKET - FIXED: Generate QR Code
    public function showTiket($id)
{
    $pemesanan = Pemesanan::with(['jadwal.film', 'jadwal.studio', 'kursi', 'user'])
        ->where('id', $id)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    // Cek status pembayaran
    if ($pemesanan->status_pembayaran !== 'sudah_bayar') {
        // Tetap tampilkan tiket tapi tanpa QR
        return view('pelanggan.tiket', compact('pemesanan'));
    }

    $bookingCode = $pemesanan->kode_pemesanan;
    
    // Generate signature
    $secretKey = config('app.qr_secret_key');
    $timestamp = time();
    $signature = hash_hmac('sha256', $bookingCode . '|' . $timestamp, $secretKey);
    $shortSig = substr($signature, 0, 16);
    
    // Generate URL untuk QR Code
    $verifyUrl = route('tiket.verify', [
        'code' => $bookingCode,
        'sig' => $shortSig,
        't' => $timestamp
    ]);

    return view('pelanggan.tiket', compact('pemesanan', 'bookingCode', 'verifyUrl'));
}
}
