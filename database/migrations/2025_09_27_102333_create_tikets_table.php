<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTiketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tikets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_kursi_id')->constrained()->onDelete('cascade');
            $table->string('kode_tiket')->unique();
            $table->text('qr_code'); // generated QR code string
            $table->enum('status_tiket', ['aktif', 'terpakai', 'dibatalkan'])->default('aktif');
            $table->datetime('tanggal_cetak')->nullable();
            $table->foreignId('divalidasi_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tikets');
    }
}
