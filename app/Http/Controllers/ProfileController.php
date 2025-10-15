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
        $tab = $request->get('tab', 'profile'); // default tab profile
        
        // Ambil riwayat pemesanan + relasi film & studio
        $riwayat = Pemesanan::with(['jadwal.film', 'jadwal.studio'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Ambil kursi untuk setiap pemesanan dan update status otomatis
        foreach ($riwayat as $pemesanan) {
            $pkTable = (new PemesananKursi())->getTable();
            $fkName = Schema::hasColumn($pkTable, 'pemesanan_id') ? 'pemesanan_id' :
                      (Schema::hasColumn($pkTable, 'booking_id') ? 'booking_id' : null);
            
            $kursiIds = [];
            if ($fkName) {
                $kursiIds = DB::table($pkTable)
                    ->where($fkName, $pemesanan->id)
                    ->pluck('kursi_id')
                    ->toArray();
            }
            
            $pemesanan->kursis = Kursi::whereIn('id', $kursiIds)->get();

            // 🔥 Update status otomatis jadi "selesai" kalau film sudah lewat
            if ($pemesanan->jadwal && $pemesanan->jadwal->jam_tayang) {
                $jamTayang = Carbon::parse($pemesanan->jadwal->jam_tayang);
                $durasi = $pemesanan->jadwal->film->durasi ?? 120; // default 2 jam
                $jamSelesai = $jamTayang->copy()->addMinutes($durasi);

                if ($pemesanan->status_pembayaran === 'sudah_bayar' && now()->greaterThan($jamSelesai)) {
                    $pemesanan->status_pembayaran = 'selesai';
                }
            }
        }
        
        return view('pelanggan.akun', compact('user', 'riwayat', 'tab'));
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
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
}
