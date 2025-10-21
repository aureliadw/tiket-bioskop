<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Film;
use App\Models\Studio;
use App\Models\Jadwal;
use App\Models\Pemesanan;
use App\Models\User;
use Carbon\Carbon;

class OwnerTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create sample films
        $films = [
            ['judul' => 'Dune Part Two', 'genre' => 'Sci-Fi', 'durasi' => 166, 'status' => 'sedang_tayang'],
            ['judul' => 'Avatar: The Way of Water', 'genre' => 'Action', 'durasi' => 192, 'status' => 'sedang_tayang'],
            ['judul' => 'Oppenheimer', 'genre' => 'Drama', 'durasi' => 180, 'status' => 'sedang_tayang'],
        ];

        foreach ($films as $filmData) {
            Film::firstOrCreate(['judul' => $filmData['judul']], $filmData);
        }

        // Create sample studios
        $studios = [
            ['nama_studio' => 'Studio 1', 'total_kursi' => 150],
            ['nama_studio' => 'Studio 2', 'total_kursi' => 100],
            ['nama_studio' => 'Studio 3', 'total_kursi' => 80],
        ];

        foreach ($studios as $studioData) {
            Studio::firstOrCreate(['nama_studio' => $studioData['nama_studio']], $studioData);
        }

        // Create jadwal for last 30 days
        $films = Film::all();
        $studios = Studio::all();
        $pelanggan = User::where('role', 'pelanggan')->first();

        if (!$pelanggan) {
            $pelanggan = User::create([
                'email' => 'customer@test.com',
                'password' => bcrypt('password'),
                'nama_lengkap' => 'Test Customer',
                'phone' => '081234567890',
                'role' => 'pelanggan'
            ]);
        }

        for ($i = 30; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i)->toDateString();
            
            foreach ($films as $film) {
                $studio = $studios->random();
                
                // Create 2-3 jadwal per day per film
                for ($j = 0; $j < rand(2, 3); $j++) {
                    $jam = sprintf("%02d:00:00", rand(10, 21));
                    
                    $jadwal = Jadwal::create([
                        'film_id' => $film->id,
                        'studio_id' => $studio->id,
                        'tanggal_tayang' => $tanggal,
                        'jam_tayang' => $jam,
                        'harga_dasar' => rand(30000, 50000),
                        'kursi_tersedia' => $studio->total_kursi,
                        'status_aktif' => true,
                    ]);

                    // Create random pemesanan (50-80% occupancy)
                    $numOrders = rand(3, 8);
                    for ($k = 0; $k < $numOrders; $k++) {
                        $jumlahKursi = rand(1, 4);
                        $totalBayar = $jumlahKursi * $jadwal->harga_dasar;
                        $tipePemesanan = rand(1, 10) > 3 ? 'online' : 'offline';
                        
                        Pemesanan::create([
                            'user_id' => $pelanggan->id,
                            'jadwal_id' => $jadwal->id,
                            'kode_pemesanan' => 'TIX' . strtoupper(substr(md5(time() . rand()), 0, 8)),
                            'jumlah_kursi' => $jumlahKursi,
                            'total_bayar' => $totalBayar,
                            'status_pemesanan' => 'dikonfirmasi',
                            'status_pembayaran' => 'sudah_bayar',
                            'tipe_pemesanan' => $tipePemesanan,
                            'tanggal_pesan' => $tanggal . ' ' . $jam,
                            'tanggal_bayar' => $tanggal . ' ' . $jam,
                        ]);
                    }
                }
            }
        }
    }
}
