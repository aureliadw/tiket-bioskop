<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembayaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')->constrained()->onDelete('cascade');
            $table->decimal('jumlah_bayar', 10, 2);
            $table->enum('metode_pembayaran', ['tunai', 'transfer_bank', 'e_wallet', 'kartu_kredit']);
            $table->string('id_transaksi')->nullable(); 
            $table->enum('status_pembayaran', ['pending', 'berhasil', 'gagal'])->default('pending');
            $table->json('detail_pembayaran')->nullable(); 
            $table->string('bukti_transfer')->nullable(); 
            $table->datetime('tanggal_bayar')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::dropIfExists('pembayarans');
    }
}
