<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableKecamatan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kecamatan', function (Blueprint $table) {
            $table->integer('kec_id')->autoIncrement();
            $table->integer('kec_kot_id');
            $table->string('kec_name', 150)->nullable(false);
            $table->char('kec_kode', 8);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_kecamatan');
    }
}
