<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableKelurahan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kelurahan', function (Blueprint $table) {
            $table->integer('kel_id')->autoIncrement();
            $table->integer('kel_kec_id');
            $table->string('kel_name', 150)->nullable(false);
            $table->char('kel_kode', 8);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_kelurahan');
    }
}
