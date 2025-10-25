<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\KelolaFilmController;
use App\Http\Controllers\Admin\KelolaJadwalController;
use App\Http\Controllers\Admin\KelolaStudioController;
use App\Http\Controllers\Admin\KelolaPelangganController;
use App\Http\Controllers\Admin\KelolaKasirController;
use App\Http\Controllers\Admin\KelolaOwnerController;
use App\Http\Controllers\Kasir\KasirController;
use App\Http\Controllers\Owner\OwnerController;
use App\Http\Controllers\TiketController;
use App\Http\Controllers\Admin\LaporanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Halaman utama pelanggan
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/now-playing', [FilmController::class, 'nowPlaying'])->name('pelanggan.now-playing');
Route::get('/coming-soon', [FilmController::class, 'comingSoon'])->name('pelanggan.coming-soon');
Route::get('/film/{id}', [FilmController::class, 'detail'])->name('pelanggan.detail');
Route::get('/search', [FilmController::class, 'search'])->name('pelanggan.search');

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Register khusus user
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () { 
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Booking jadwal / pilih jam film
    Route::post('/film/{id}/jadwal', [FilmController::class, 'pesanJadwal'])->name('pelanggan.pesan-jadwal');

    // 🔹 Pilih Kursi
    Route::get('/jadwal/{id}/kursi', [BookingController::class, 'pilihKursi'])->name('pelanggan.pilih-kursi');
    Route::post('/jadwal/{id}/kursi', [BookingController::class, 'prosesKursi'])->name('pelanggan.proses-kursi');
    
    // ✅ Generate Snap Token
    Route::post('/booking/payment/{pemesanan}', [BookingController::class, 'generateSnapToken'])->name('booking.generate-snap');
    
    // ✅ Check Payment Status
    Route::get('/booking/check-status/{orderId}', [BookingController::class, 'checkPaymentStatus'])->name('booking.check-status');

    Route::post('/midtrans/callback', [BookingController::class, 'handleCallback']);

    /* 🔥 MIDTRANS: Check Payment Status
    Route::get('/pembayaran/check-status/{orderId}', [PembayaranController::class, 'checkPaymentStatus'])->name('pembayaran.check-status');

    // pembayaran - route lama (tetap ada)
    Route::get('/pembayaran/{pemesanan_id}', [PembayaranController::class, 'show'])->name('pembayaran.show');
    Route::post('/pembayaran/{pemesanan_id}', [PembayaranController::class, 'process'])->name('pembayaran.process');

    // route baru untuk alur pembayaran yang diperbaiki
    Route::post('/pembayaran/proses/{pemesanan}', [PembayaranController::class, 'prosesPembayaran'])->name('pelanggan.proses-pembayaran');
    Route::post('/pembayaran/upload-bukti/{pemesanan}', [PembayaranController::class, 'uploadBukti'])->name('pelanggan.upload-bukti');*/

    // Profile & Riwayat
    Route::get('/akun', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/akun/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/akun/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::delete('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/tiket/{pemesanan_id}', [BookingController::class, 'showTiket'])->name('pelanggan.tiket');

    Route::get('/tiket/verify/{code}', [TiketController::class, 'verify'])->name('tiket.verify');
});

/*
|--------------------------------------------------------------------------
| Protected Routes untuk role tertentu
|--------------------------------------------------------------------------
*/
// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Verifikasi Pembayaran
    Route::get('/pembayaran', [AdminController::class, 'pembayaran'])->name('pembayaran');
    
    // ✅ PEMESANAN (Update ini)
    Route::get('/pemesanan', [AdminController::class, 'pemesanan'])->name('pemesanan');
    Route::get('/pemesanan/{id}', [AdminController::class, 'pemesananDetail'])->name('pemesanan.detail');

    Route::resource('film', KelolaFilmController::class);

    // Route bulk jadwal
    Route::get('/jadwal/bulk', [KelolaJadwalController::class, 'bulkCreate'])->name('jadwal.bulk.create');
    Route::post('/jadwal/bulk', [KelolaJadwalController::class, 'bulkStore'])->name('jadwal.bulk.store');
    Route::resource('jadwal', KelolaJadwalController::class);
    
    Route::resource('studio', KelolaStudioController::class);

    // ✅ Kelola Pengguna
    Route::resource('pelanggan', KelolaPelangganController::class);
    Route::resource('kasir', KelolaKasirController::class);
    Route::resource('owner', KelolaOwnerController::class);

    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        
        // Laporan Transaksi
        Route::get('/transaksi', [LaporanController::class, 'transaksi'])->name('transaksi');
        Route::get('/transaksi/pdf', [LaporanController::class, 'transaksiPdf'])->name('transaksi.pdf');
        
        // Laporan Pendapatan
        Route::get('/pendapatan', [LaporanController::class, 'pendapatan'])->name('pendapatan');
        Route::get('/pendapatan/pdf', [LaporanController::class, 'pendapatanPdf'])->name('pendapatan.pdf');
        
        // Laporan Film
        Route::get('/film', [LaporanController::class, 'film'])->name('film');
        Route::get('/film/pdf', [LaporanController::class, 'filmPdf'])->name('film.pdf');
    });
});

// ========== KASIR ROUTES ==========
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');
    
    // Check-in Tiket
    Route::get('/checkin', [KasirController::class, 'checkInPage'])->name('checkin');
    Route::post('/check-by-code', [KasirController::class, 'checkByCode'])->name('check-by-code');
    Route::post('/scan-tiket', [KasirController::class, 'scanTiket'])->name('scan-tiket');
    Route::post('/use-tiket/{id}', [KasirController::class, 'useTiket'])->name('use-tiket');
    
    // Print Tiket
    Route::get('/print/{id}', [KasirController::class, 'printTiket'])->name('print.tiket');
    
    // Jual Tiket Offline
    Route::get('/jual-tiket', [KasirController::class, 'jualTiketPage'])->name('jual-tiket');
    Route::get('/get-kursi/{jadwalId}', [KasirController::class, 'getKursiTersedia'])->name('get-kursi');
    
    // Store Tiket Offline (CASH)
    Route::post('/store-tiket-offline', [KasirController::class, 'storeTiketOffline'])->name('store-tiket-offline');
    
    // Generate Snap Token (DIGITAL)
    Route::post('/generate-snap-token-offline', [KasirController::class, 'generateSnapTokenOffline'])->name('generate-snap-token-offline');
    
    // Check Payment Status (DIGITAL)
    Route::get('/check-payment-status-offline/{orderId}', [KasirController::class, 'checkPaymentStatusOffline'])->name('check-payment-status-offline');
    
    // Verifikasi Pembayaran
    Route::get('/verifikasi-pembayaran', [KasirController::class, 'verifikasiPembayaran'])->name('verifikasi-pembayaran');
    Route::post('/konfirmasi-pembayaran/{id}', [KasirController::class, 'konfirmasiPembayaran'])->name('konfirmasi-pembayaran');
    Route::post('/tolak-pembayaran/{id}', [KasirController::class, 'tolakPembayaran'])->name('tolak-pembayaran');
});

// Owner
Route::middleware(['auth', 'role:owner'])->prefix('owner')->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'index'])->name('owner.dashboard');
    Route::get('/revenue-report', [OwnerController::class, 'revenueReport'])->name('owner.revenue.report');
    Route::get('/film-performance', [OwnerController::class, 'filmPerformance'])->name('owner.film.performance');
    Route::get('/export-pdf', [OwnerController::class, 'exportPDF'])->name('owner.export.pdf');
});

Route::post('/midtrans/webhook', [BookingController::class, 'midtransWebhook'])->name('midtrans.webhook');

