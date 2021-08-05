<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKartuBerobatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kartu_berobat', function (Blueprint $table) {
            $table->id();
            $table->integer('no_kartu');
            $table->integer('id_antrian');
            $table->integer('id_pasien');
            $table->integer('id_dokter');
            $table->string('diagnosa');
            $table->string('keadaan_umum');
            $table->string('occlusi');
            $table->string('kebersihan_mulut');
            $table->string('perawatan');
            $table->integer('biaya');
            $table->string('catatan');
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
        Schema::dropIfExists('kartu_berobat');
    }
}
