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
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'profile');
        
        // Auto-delete pemesanan expired (belum bayar > 10 menit)
        $this->deleteExpiredBookings($user->id);
        
        // Ambil riwayat pemesanan
        $riwayat = Pemesanan::with(['jadwal.film', 'jadwal.studio', 'pembayaran'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Load kursi dan update status
        $this->loadSeatsAndUpdateStatus($riwayat);
        
        return view('pelanggan.akun', compact('user', 'riwayat', 'tab'));
    }
    
    /**
     * Update profil user
     */
    public function update(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
        ]);
        
        $user = Auth::user();
        $user->nama_lengkap = $request->nama_lengkap;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();
        
        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Ganti password user
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->with('error', 'Password lama tidak sesuai');
        }

        Auth::user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('profile.index')->with('success', 'Password berhasil diubah!');
    }

    /**
     * Hapus pemesanan yang expired (belum bayar > 10 menit)
     */
    private function deleteExpiredBookings($userId)
    {
        Pemesanan::where('user_id', $userId)
            ->where('status_pembayaran', 'belum_bayar')
            ->where('created_at', '<', now()->subMinutes(10))
            ->delete();
    }

    /**
     * Load kursi dan update status pemesanan
     */
    private function loadSeatsAndUpdateStatus($riwayat)
    {
        foreach ($riwayat as $pemesanan) {
            // Load kursi
            $pemesanan->kursis = $this->getBookingSeats($pemesanan->id);

            // Update status jadi "selesai" kalau film sudah lewat
            $this->updateBookingStatusIfExpired($pemesanan);
        }
    }

    /**
     * Ambil kursi dari pemesanan
     */
    private function getBookingSeats($pemesananId)
    {
        $pkTable = (new PemesananKursi())->getTable();
        $fkName = Schema::hasColumn($pkTable, 'pemesanan_id') ? 'pemesanan_id' :
                  (Schema::hasColumn($pkTable, 'booking_id') ? 'booking_id' : null);
        
        if (!$fkName) {
            return collect([]);
        }
        
        $kursiIds = DB::table($pkTable)
            ->where($fkName, $pemesananId)
            ->pluck('kursi_id')
            ->toArray();
        
        return Kursi::whereIn('id', $kursiIds)->get();
    }

    /**
     * Update status pemesanan jadi "selesai" kalau film sudah lewat
     */
    private function updateBookingStatusIfExpired($pemesanan)
    {
        if (!$pemesanan->jadwal || !$pemesanan->jadwal->jam_tayang) {
            return;
        }

        $jamTayang = Carbon::parse($pemesanan->jadwal->jam_tayang);
        $durasi = $pemesanan->jadwal->film->durasi ?? 120;
        $jamSelesai = $jamTayang->copy()->addMinutes($durasi);

        if ($pemesanan->status_pembayaran === 'sudah_bayar' && now()->greaterThan($jamSelesai)) {
            $pemesanan->status_pembayaran = 'selesai';
            $pemesanan->save();
        }
    }
}
