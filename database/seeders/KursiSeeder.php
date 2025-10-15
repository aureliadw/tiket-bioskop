<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kursi;
use App\Models\Studio;

class KursiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Hapus kursi lama (opsional)
        Kursi::query()->delete();

        $studios = [
            // studio_id => [jumlah_baris, jumlah_kolom]
            1 => ['rows' => 10, 'cols' => 10], // Studio 1: 100 kursi (A-J, 1-10)
            2 => ['rows' => 10,  'cols' => 10], // Studio 2: 50 kursi (A-E, 1-10)
            3 => ['rows' => 8,  'cols' => 10],  // Studio 3: 40 kursi (A-E, 1-8)
            4 => ['rows' => 6,  'cols' => 10], // Studio 4: 60 kursi (A-F, 1-10)
        ];

        foreach ($studios as $studioId => $config) {
            // Cek apakah studio ada
            if (!Studio::find($studioId)) {
                $this->command->warn("Studio {$studioId} tidak ditemukan, skip...");
                continue;
            }

            $rows = range('A', chr(64 + $config['rows'])); // A-J untuk 10 baris
            $cols = range(1, $config['cols']);

            foreach ($rows as $row) {
                foreach ($cols as $col) {
                    Kursi::create([
                        'studio_id'   => $studioId,
                        'nomor_kursi' => $row . $col, // A1, A2, B1, dll
                        'baris'       => $row,
                        'kolom'       => $col,
                        'status_aktif'=> true,
                    ]);
                }
            }

            $total = $config['rows'] * $config['cols'];
            $this->command->info("✓ Studio {$studioId}: {$total} kursi berhasil dibuat!");
        }

        $this->command->info("\n✅ Total kursi: " . Kursi::count());
    }
}
