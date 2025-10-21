<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pemesanan;
use App\Models\PemesananKursi;
use App\Models\Kursi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * ============================================
     * INDEX - Tampilkan Halaman Profile/Riwayat
     * ============================================
     * 
     * Fitur:
     * 1. Auto-delete pemesanan expired (belum bayar > 10 menit)
     * 2. Ambil riwayat pemesanan user
     * 3. Load kursi untuk setiap pemesanan
     * 4. Update status pemesanan yang sudah lewat
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'profile');
        
        // AUTO-DELETE: Pemesanan expired (belum bayar > 10 menit)
        $this->deleteExpiredBookings($user->id);
        
        // AMBIL RIWAYAT PEMESANAN
        $riwayat = Pemesanan::with(['jadwal.film', 'jadwal.studio', 'pembayaran'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // LOAD KURSI & UPDATE STATUS
        $this->loadSeatsAndUpdateStatus($riwayat);
        
        return view('pelanggan.akun', compact('user', 'riwayat', 'tab'));
    }
    
    /**
     * ============================================
     * UPDATE - Update Profil atau Delete Account
     * ============================================
     * 
     * Fitur:
     * - Update: nama_lengkap, email, phone
     * - Delete Account: hapus user dari database
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // CEK: Apakah user klik "Delete Account"?
        if ($request->has('delete_account')) {
            Auth::logout();
            $user->delete();
            return redirect('/')->with('success', 'Akun berhasil dihapus.');
        }

        // VALIDASI INPUT
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        // UPDATE PROFILE
        $user->update([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * ============================================
     * CHANGE PASSWORD - Ganti Password User
     * ============================================
     * 
     * Validasi:
     * - Password lama harus benar
     * - Password baru minimal 8 karakter
     * - Konfirmasi password harus sama
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        // VALIDASI INPUT
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // CEK PASSWORD LAMA
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->with('error', 'Password lama tidak sesuai');
        }

        // UPDATE PASSWORD BARU
        Auth::user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Password berhasil diubah!');
    }

    /**
     * ============================================
     * PRIVATE: Delete Expired Bookings
     * ============================================
     * 
     * Hapus pemesanan yang:
     * - Status: belum_bayar
     * - Created > 10 menit yang lalu
     * 
     * @param int $userId
     * @return void
     */
    private function deleteExpiredBookings($userId)
    {
        Pemesanan::where('user_id', $userId)
            ->where('status_pembayaran', 'belum_bayar')
            ->where('created_at', '<', now()->subMinutes(10))
            ->delete();
    }

    /**
     * ============================================
     * PRIVATE: Load Seats & Update Status
     * ============================================
     * 
     * Untuk setiap pemesanan:
     * 1. Load kursi yang dipesan
     * 2. Update status jadi "selesai" kalau film sudah lewat
     * 
     * @param \Illuminate\Support\Collection $riwayat
     * @return void
     */
    private function loadSeatsAndUpdateStatus($riwayat)
    {
        foreach ($riwayat as $pemesanan) {
            // LOAD KURSI
            $pemesanan->kursis = $this->getBookingSeats($pemesanan->id);

            // UPDATE STATUS (jika film sudah lewat)
            $this->updateBookingStatusIfExpired($pemesanan);
        }
    }

    /**
     * ============================================
     * PRIVATE: Get Booking Seats
     * ============================================
     * 
     * Ambil semua kursi dari pemesanan tertentu
     * 
     * Kompatibilitas:
     * - Cek apakah kolom foreign key bernama 'pemesanan_id' atau 'booking_id'
     * 
     * @param int $pemesananId
     * @return \Illuminate\Support\Collection
     */
    private function getBookingSeats($pemesananId)
    {
        $pkTable = (new PemesananKursi())->getTable();
        
        // CEK NAMA KOLOM FOREIGN KEY
        $fkName = Schema::hasColumn($pkTable, 'pemesanan_id') ? 'pemesanan_id' :
                  (Schema::hasColumn($pkTable, 'booking_id') ? 'booking_id' : null);
        
        // Jika tidak ada kolom yang cocok
        if (!$fkName) {
            return collect([]);
        }
        
        // AMBIL KURSI IDs
        $kursiIds = DB::table($pkTable)
            ->where($fkName, $pemesananId)
            ->pluck('kursi_id')
            ->toArray();
        
        // RETURN KURSI OBJECTS
        return Kursi::whereIn('id', $kursiIds)->get();
    }

    /**
     * ============================================
     * PRIVATE: Update Booking Status If Expired
     * ============================================
     * 
     * Jika film sudah selesai tayang (jam_tayang + durasi < now),
     * update status_pembayaran jadi "sudah_bayar" (selesai)
     * 
     * @param \App\Models\Pemesanan $pemesanan
     * @return void
     */
    private function updateBookingStatusIfExpired($pemesanan)
    {
        // CEK: Apakah jadwal & jam_tayang ada?
        if (!$pemesanan->jadwal || !$pemesanan->jadwal->jam_tayang) {
            return;
        }

        // HITUNG JAM SELESAI (jam_tayang + durasi film)
        $jamTayang = Carbon::parse($pemesanan->jadwal->jam_tayang);
        $durasi = $pemesanan->jadwal->film->durasi ?? 120; // default 120 menit
        $jamSelesai = $jamTayang->copy()->addMinutes($durasi);

        // UPDATE STATUS jika:
        // - Status: sudah_bayar
        // - Waktu sekarang > jam selesai
        if ($pemesanan->status_pembayaran === 'sudah_bayar' && now()->greaterThan($jamSelesai)) {
            // Update langsung ke database
            Pemesanan::where('id', $pemesanan->id)
                ->update(['status_pembayaran' => 'sudah_bayar']);
            
            // Sync property model
            $pemesanan->status_pembayaran = 'sudah_bayar';
        }
    }
}
