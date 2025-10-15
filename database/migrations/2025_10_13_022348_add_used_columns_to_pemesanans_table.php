<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsedColumnsToPemesanansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            // ✅ Tambah 2 kolom untuk tracking penggunaan tiket
            $table->timestamp('used_at')->nullable()->after('tanggal_bayar');
            $table->foreignId('used_by')->nullable()->after('used_at')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->dropForeign(['used_by']);
            $table->dropColumn(['used_at', 'used_by']);
        });
    }
}
