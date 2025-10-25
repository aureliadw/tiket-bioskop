<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateMetodePembayaranEnumInPembayaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update enum untuk metode_pembayaran
        DB::statement("ALTER TABLE `pembayarans` MODIFY `metode_pembayaran` ENUM('tunai', 'transfer_bank', 'e_wallet', 'kartu_kredit', 'midtrans') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `pembayarans` MODIFY `metode_pembayaran` ENUM('tunai', 'transfer_bank', 'e_wallet', 'kartu_kredit') NOT NULL");
    }
}
