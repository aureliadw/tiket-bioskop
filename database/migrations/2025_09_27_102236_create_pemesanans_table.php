<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemesanansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('jadwal_id')->constrained()->onDelete('cascade');
            $table->string('kode_pemesanan')->unique();
            $table->integer('jumlah_kursi');
            $table->decimal('total_bayar', 10, 2);
            $table->enum('status_pemesanan', ['pending', 'dikonfirmasi', 'dibatalkan', 'digunakan'])->default('pending');
            $table->enum('status_pembayaran', ['belum_bayar', 'sudah_bayar', 'refund'])->default('belum_bayar');
            $table->enum('tipe_pemesanan', ['online', 'offline'])->default('online');
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('tanggal_pesan');
            $table->datetime('tanggal_bayar')->nullable();
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
        Schema::dropIfExists('pemesanans');
    }
}
