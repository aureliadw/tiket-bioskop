<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('genre')->nullable();
            $table->integer('durasi')->nullable(); // in minutes
            $table->string('sutradara')->nullable();
            $table->string('produser')->nullable();
            $table->string('produksi')->nullable();
            $table->string('penulis')->nullable();
            $table->text('pemain')->nullable();
            $table->string('poster_image')->nullable();
            $table->string('trailer_video')->nullable();
            $table->decimal('rating', 3, 1)->default(0);
            $table->enum('status', ['sedang_tayang', 'akan_tayang', 'tidak_tayang'])->default('akan_tayang');
            $table->date('tanggal_rilis')->nullable();
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
        Schema::dropIfExists('films');
    }
}
