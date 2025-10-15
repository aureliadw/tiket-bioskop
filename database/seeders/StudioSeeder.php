<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Studio;

class StudioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $studios = [
            [
                'nama_studio'   => 'Studio 1',
                'total_kursi'   => 50,
                'deskripsi'     => 'Studio standar dengan kapasitas 50 kursi.',
                'status_aktif'  => true,
            ],
            [
                'nama_studio' => 'Studio 2',
                'total_kursi' => 50,
                'deskripsi' => 'Studio standar dengan kapasitas 50 kursi.',
                'status_aktif' => 1,
            ],
            [
                'nama_studio' => 'Studio 3',
                'total_kursi' => 40,
                'deskripsi' => 'Studio kecil dengan kapasitas 40 kursi.',
                'status_aktif' => 1,
            ],
            [
                'nama_studio' => 'Studio 4',
                'total_kursi' => 60,
                'deskripsi' => 'Studio besar dengan kapasitas 60 kursi.',
                'status_aktif' => 1,
            ],
        ];

        foreach ($studios as $studio) {
            // Cek dulu apakah studio sudah ada
            if (!Studio::where('nama_studio', $studio['nama_studio'])->exists()) {
                Studio::create($studio);
                $this->command->info("✓ {$studio['nama_studio']} berhasil ditambahkan!");
            }
        }

        $this->command->info("\n✅ Total studio: " . Studio::count());
    }
}
