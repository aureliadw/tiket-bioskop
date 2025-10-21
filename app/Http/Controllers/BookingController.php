<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Film;
use App\Models\Jadwal;
use App\Models\Kursi;
use App\Models\Pemesanan;
use App\Models\PemesananKursi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * ============================================
     * PILIH KURSI - Tampilkan Layout Kursi Studio
     * ============================================
     * 
     * Fitur Utama:
     * 1. Auto-release pemesanan expired (> 10 menit & belum bayar)
     * 2. Tampilkan semua kursi di studio
     * 3. Tandai kursi TERJUAL (sudah_bayar)
     * 4. Tandai kursi PENDING (belum_bayar & < 10 menit)
     * 
     * @param int $jadwalId - ID Jadwal
     * @return \Illuminate\View\View
     */
    public function pilihKursi($jadwalId)
    {
        // 1. AUTO-RELEASE: Hapus pemesanan expired
        // ==========================================
        // Jika user booking tapi tidak bayar dalam 10 menit,
        // kursinya akan otomatis di-release untuk user lain
        Pemesanan::where('status_pembayaran', 'belum_bayar')
            ->where('created_at', '<', now()->subMinutes(10))
            ->delete();

        // 2. AMBIL DATA JADWAL (dengan relasi Film & Studio)
        // ===================================================
        $jadwal = Jadwal::with(['film', 'studio'])->findOrFail($jadwalId);
        
        // 3. AMBIL SEMUA KURSI DI STUDIO INI
        // ===================================
        // Diurutkan berdasarkan baris (A, B, C) dan kolom (1, 2, 3)
        $kursis = Kursi::where('studio_id', $jadwal->studio_id)
            ->orderBy('baris')
            ->orderBy('kolom')
            ->get();

        // 4. AMBIL KURSI TERJUAL (Status Pembayaran: sudah_bayar)
        // ========================================================
        // Kursi ini TIDAK BISA dipilih lagi (permanent sold)
        $kursiTerjual = DB::table('pemesanan_kursis')
            ->join('pemesanans', 'pemesanan_kursis.pemesanan_id', '=', 'pemesanans.id')
            ->where('pemesanans.jadwal_id', $jadwalId)
            ->where('pemesanans.status_pembayaran', 'sudah_bayar')
            ->pluck('pemesanan_kursis.kursi_id')
            ->toArray();

        // 5. AMBIL KURSI PENDING (Belum bayar & masih dalam 10 menit)
        // ============================================================
        // Kursi ini sedang "di-hold" oleh user lain yang belum checkout
        // Setelah 10 menit, akan otomatis di-release (lihat step 1)
        $kursiPending = DB::table('pemesanan_kursis')
            ->join('pemesanans', 'pemesanan_kursis.pemesanan_id', '=', 'pemesanans.id')
            ->where('pemesanans.jadwal_id', $jadwalId)
            ->where('pemesanans.status_pembayaran', 'belum_bayar')
            ->where('pemesanans.created_at', '>=', now()->subMinutes(10)) // Masih fresh (< 10 menit)
            ->pluck('pemesanan_kursis.kursi_id')
            ->toArray();

        return view('pelanggan.pilih-kursi', compact('jadwal', 'kursis', 'kursiTerjual', 'kursiPending'));
    }

    /**
     * ============================================
     * PROSES KURSI - Simpan Pemesanan ke Database
     * ============================================
     * 
     * Flow:
     * 1. Validasi user sudah login
     * 2. Validasi input kursi (minimal 1)
     * 3. Hitung harga (weekend = 45K, weekday = harga_dasar)
     * 4. Buat record Pemesanan
     * 5. Simpan kursi yang dipilih ke pemesanan_kursis
     * 6. Redirect ke halaman pembayaran
     * 
     * @param Request $request
     * @param int $jadwal_id - ID Jadwal
     * @return \Illuminate\Http\RedirectResponse
     */
    public function prosesKursi(Request $request, $jadwal_id)
    {
        // 1. CEK LOGIN
        // ============
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. VALIDASI INPUT KURSI
        // ========================
        // Minimal 1 kursi harus dipilih
        // Semua kursi harus valid (ada di tabel kursis)
        $request->validate([
            'kursi' => 'required|array|min:1',
            'kursi.*' => 'integer|exists:kursis,id',
        ], [
            'kursi.required' => 'Silakan pilih minimal 1 kursi.',
            'kursi.min' => 'Silakan pilih minimal 1 kursi.',
        ]);

        // 3. AMBIL DATA JADWAL
        // =====================
        $jadwal = Jadwal::findOrFail($jadwal_id);

        // 4. HITUNG HARGA TIKET (Weekend vs Weekday)
        // ===========================================
        $hargaDasar = $jadwal->harga_dasar;
        $tanggalTayang = Carbon::parse($jadwal->tanggal_tayang);
        $isWeekend = $tanggalTayang->isWeekend(); // Sabtu/Minggu?
        
        // Jika weekend → Rp 45.000, jika weekday → pakai harga_dasar
        $hargaFinal = $isWeekend ? 45000 : $hargaDasar;

        // 5. SIMPAN KE DATABASE (dengan Transaction)
        // ===========================================
        // Transaction memastikan semua query sukses atau semua gagal (rollback)
        DB::beginTransaction();
        
        try {
            // GENERATE KODE PEMESANAN UNIK
            // Format: TIX-67890ABCDEF
            $kodePemesanan = 'TIX-' . strtoupper(uniqid());

            // BUAT RECORD PEMESANAN
            $pemesanan = Pemesanan::create([
                'user_id'           => Auth::id(),
                'jadwal_id'         => $jadwal->id,
                'kode_pemesanan'    => $kodePemesanan,
                'jumlah_kursi'      => count($request->kursi),
                'total_bayar'       => count($request->kursi) * $hargaFinal,
                'status_pemesanan'  => 'pending',
                'status_pembayaran' => 'belum_bayar',
                'tipe_pemesanan'    => 'online',
                'tanggal_pesan'     => now(),
            ]);

            // SIMPAN DETAIL KURSI KE PIVOT TABLE (pemesanan_kursis)
            foreach ($request->kursi as $kursiId) {
                DB::table('pemesanan_kursis')->insert([
                    'pemesanan_id' => $pemesanan->id,
                    'kursi_id'     => $kursiId,
                    'harga_kursi'  => $hargaFinal, // PENTING: Pakai harga final (bukan dasar)
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            // COMMIT: Semua query berhasil
            DB::commit();

            // REDIRECT KE HALAMAN PEMBAYARAN
            return redirect()->route('pembayaran.show', $pemesanan->id)
                ->with('success', 'Kursi berhasil dipilih. Silakan selesaikan pembayaran dalam 10 menit.');
            
        } catch (\Throwable $e) {
            // ROLLBACK: Batalkan semua perubahan jika ada error
            DB::rollBack();
            
            // LOG ERROR untuk debugging
            Log::error('Proses kursi gagal: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            // TAMPILKAN ERROR KE USER
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
