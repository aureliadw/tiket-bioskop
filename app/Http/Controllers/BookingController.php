<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Film;
use App\Models\Jadwal;
use App\Models\Kursi;
use App\Models\Pemesanan;
use App\Models\PemesananKursi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function pilihKursi($jadwal_id)
{
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Silakan login untuk memesan kursi.');
    }

    $jadwal = Jadwal::with(['film', 'studio'])->findOrFail($jadwal_id);
    $film = $jadwal->film;

    $kursis = Kursi::where('studio_id', $jadwal->studio_id)
        ->orderBy('baris')
        ->orderBy('kolom')
        ->get();

    $pkTable = (new PemesananKursi())->getTable();
    
    // Kursi TERJUAL (merah) - sudah bayar untuk jadwal hari ini
    $kursiTerjual = DB::table($pkTable)
        ->join('pemesanans', $pkTable . '.pemesanan_id', '=', 'pemesanans.id')
        ->where('pemesanans.jadwal_id', $jadwal_id)
        ->where('pemesanans.status_pembayaran', 'sudah_bayar')
        ->whereDate('pemesanans.created_at', '>=', now()->startOfDay())
        ->pluck($pkTable . '.kursi_id')
        ->toArray();

    // Kursi PENDING (kuning) - belum bayar untuk jadwal hari ini
    $kursiPending = DB::table($pkTable)
        ->join('pemesanans', $pkTable . '.pemesanan_id', '=', 'pemesanans.id')
        ->where('pemesanans.jadwal_id', $jadwal_id)
        ->where('pemesanans.status_pembayaran', 'belum_bayar')
        ->whereDate('pemesanans.created_at', '>=', now()->startOfDay())
        ->pluck($pkTable . '.kursi_id')
        ->toArray();

    return view('pelanggan.pilih-kursi', compact('film', 'jadwal', 'kursis', 'kursiTerjual', 'kursiPending'));
}

    public function prosesKursi(Request $request, $jadwal_id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $request->validate([
            'kursi' => 'required|array|min:1',
            'kursi.*' => 'integer|exists:kursis,id',
        ]);

        $jadwal = Jadwal::findOrFail($jadwal_id);
        $hargaDasar = floatval($jadwal->harga_dasar ?? 0);

        DB::beginTransaction();
        try {
            // ✅ GENERATE KODE BOOKING FORMAT BARU: TIX-YYYYMMDD-XXXX
            $today = date('Ymd'); // 20250114
            
            // Hitung pemesanan hari ini untuk sequence
            $count = Pemesanan::whereDate('created_at', today())->count();
            
            // Format: TIX-YYYYMMDD-XXXX
            $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            $kodePemesanan = "TIX-{$today}-{$sequence}";
            
            Log::info("Generated booking code: {$kodePemesanan}");

            $pemesanan = Pemesanan::create([
                'user_id'           => Auth::id(),
                'jadwal_id'         => $jadwal->id,
                'kode_pemesanan'    => $kodePemesanan, // ✅ Format baru
                'jumlah_kursi'      => count($request->kursi),
                'total_bayar'       => count($request->kursi) * $hargaDasar,
                'status_pemesanan'  => 'pending',
                'status_pembayaran' => 'belum_bayar',
                'tipe_pemesanan'    => 'online',
                'tanggal_pesan'     => now(),
            ]);

            foreach ($request->kursi as $kursiId) {
                DB::table('pemesanan_kursis')->insert([
                    'pemesanan_id' => $pemesanan->id,
                    'kursi_id'     => $kursiId,
                    'harga_kursi'  => $hargaDasar,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            DB::commit();
            
            Log::info("Booking created successfully: {$kodePemesanan}, Pemesanan ID: {$pemesanan->id}");

            return redirect()->route('pembayaran.show', $pemesanan->id)
                ->with('success', 'Kursi berhasil dipilih, silakan lanjut ke pembayaran.');
                
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Proses kursi gagal: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
