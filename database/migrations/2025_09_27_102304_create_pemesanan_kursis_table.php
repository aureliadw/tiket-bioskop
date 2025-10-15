<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemesananKursisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pemesanan_kursis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')->constrained()->onDelete('cascade');
            $table->foreignId('kursi_id')->constrained()->onDelete('cascade');
            $table->decimal('harga_kursi', 10, 2); // snapshot price at booking
            $table->timestamps();
            
            $table->unique(['pemesanan_id', 'kursi_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pemesanan_kursis');
    }
}
