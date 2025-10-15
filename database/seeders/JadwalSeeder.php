<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Film;
use App\Models\Studio;
use App\Models\Jadwal;
use carbon\Carbon;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Jadwal::query()->delete();

        $films = Film::where('status', 'sedang_tayang')->get();
        $studios = Studio::all();

        if ($studios->isEmpty() || $films->isEmpty()) {
            $this->command->error('Data tidak lengkap!');
            return;
        }

        $jamTayang = ['11:30:00', '14:00:00', '16:30:00', '19:00:00', '21:30:00'];

        // Buat jadwal untuk 2 hari (hari ini & besok)
        for ($day = 0; $day < 2; $day++) {
            $tanggal = Carbon::now()->addDays($day)->toDateString();

            $this->command->info("📅 Membuat jadwal untuk: {$tanggal}");

            // Tracking: studio_id-jam => film_id yang sudah terpakai
            $usedSlots = [];

            // Setiap film harus dapat 3-4 jadwal per hari
            foreach ($films as $film) {
                $jadwalCount = 0;
                $targetJadwal = rand(3, 4); // Random 3-4 jadwal per film per hari
                $attempts = 0;
                $maxAttempts = 50;

                while ($jadwalCount < $targetJadwal && $attempts < $maxAttempts) {
                    $attempts++;

                    // Pilih studio random
                    $studio = $studios->random();
                    
                    // Pilih jam random
                    $jam = $jamTayang[array_rand($jamTayang)];
                    
                    // Buat slot key untuk cek bentrok
                    $slotKey = $studio->id . '-' . $jam;

                    // Cek apakah slot kosong
                    if (!isset($usedSlots[$slotKey])) {
                        // Buat jadwal
                        Jadwal::create([
                            'film_id'        => $film->id,
                            'studio_id'      => $studio->id,
                            'tanggal_tayang' => $tanggal,
                            'jam_tayang'     => $jam,
                            'harga_dasar'    => 35000,
                            'kursi_tersedia' => $studio->total_kursi ?? 50,
                            'status_aktif'   => true,
                        ]);

                        // Tandai slot sudah terpakai
                        $usedSlots[$slotKey] = $film->id;
                        $jadwalCount++;

                        $this->command->line("  ✓ {$film->judul} → {$studio->nama_studio} @ {$jam}");
                    }
                }

                if ($jadwalCount < $targetJadwal) {
                    $this->command->warn("  ⚠ {$film->judul} hanya dapat {$jadwalCount}/{$targetJadwal} jadwal (slot penuh)");
                }
            }
        }

        // Statistik
        $this->command->info("\n📊 STATISTIK JADWAL:");
        foreach ($films as $film) {
            $count = Jadwal::where('film_id', $film->id)->count();
            $this->command->line("  • {$film->judul}: {$count} jadwal");
        }
        
        $this->command->info("\n✅ Total " . Jadwal::count() . " jadwal berhasil dibuat!");
    }
}

