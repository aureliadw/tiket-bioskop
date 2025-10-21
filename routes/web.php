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

    // pembayaran - route lama (tetap ada)
    Route::get('/pembayaran/{pemesanan_id}', [PembayaranController::class, 'show'])->name('pembayaran.show');
    Route::post('/pembayaran/{pemesanan_id}', [PembayaranController::class, 'process'])->name('pembayaran.process');

    // route baru untuk alur pembayaran yang diperbaiki
    Route::post('/pembayaran/proses/{pemesanan}', [PembayaranController::class, 'prosesPembayaran'])->name('pelanggan.proses-pembayaran');
    Route::post('/pembayaran/upload-bukti/{pemesanan}', [PembayaranController::class, 'uploadBukti'])->name('pelanggan.upload-bukti');

    // Profile & Riwayat
    Route::get('/akun', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/akun/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/akun/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::delete('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/tiket/{pemesanan_id}', [PembayaranController::class, 'showTiket'])->name('pelanggan.tiket');

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
});

Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->group(function () {
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('kasir.dashboard');
    
    Route::get('/verifikasi-pembayaran', [KasirController::class, 'verifikasiPembayaran'])->name('kasir.verifikasi');
    Route::post('/pembayaran/{id}/konfirmasi', [KasirController::class, 'konfirmasiPembayaran'])->name('kasir.pembayaran.konfirmasi');
    Route::post('/pembayaran/{id}/tolak', [KasirController::class, 'tolakPembayaran'])->name('kasir.pembayaran.tolak');

    // Check-In
    Route::get('/check-in', [KasirController::class, 'checkInPage'])->name('kasir.checkin');
    Route::post('/check-in/code', [KasirController::class, 'checkByCode'])->name('kasir.checkin.code');
    Route::post('/check-in/scan', [KasirController::class, 'scanTiket'])->name('kasir.checkin.scan');
    Route::post('/check-in/use/{id}', [KasirController::class, 'useTiket'])->name('kasir.checkin.use');
    
    // Jual Tiket Offline
    Route::get('/jual-tiket', [KasirController::class, 'jualTiketPage'])->name('kasir.jual-tiket');
    Route::get('/get-kursi/{jadwalId}', [KasirController::class, 'getKursiTersedia'])->name('kasir.get-kursi');
    Route::post('/store-tiket-offline', [KasirController::class, 'storeTiketOffline'])->name('kasir.store-tiket-offline');
    
    // Print Tiket
    Route::get('/print/{id}', [KasirController::class, 'printTiket'])->name('kasir.print.tiket');
});

// Owner
Route::middleware(['auth', 'role:owner'])->prefix('owner')->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'index'])->name('owner.dashboard');
    Route::get('/revenue-report', [OwnerController::class, 'revenueReport'])->name('owner.revenue.report');
    Route::get('/film-performance', [OwnerController::class, 'filmPerformance'])->name('owner.film.performance');
    Route::get('/export-pdf', [OwnerController::class, 'exportPDF'])->name('owner.export.pdf');
});