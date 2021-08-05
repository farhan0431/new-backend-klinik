<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdentitasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('identitas', function (Blueprint $table) {
            $table->id();
            $table->integer('id_pasien');
            $table->string('nama');
            $table->string('alamat');
            $table->integer('umur');
            $table->integer('jk')->comment('1: Pria | 2: Wanita');
            $table->string('suku');
            $table->date('tanggal_lahir');
            $table->string('telp');
            $table->string('pekerjaan');
            $table->string('keluhan_umum');
            $table->string('goldar');
            $table->string('riwayat_penyakit');
            $table->string('alergi_obat');
            $table->string('alergi_makanan');
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
        Schema::dropIfExists('identitas');
    }
}
