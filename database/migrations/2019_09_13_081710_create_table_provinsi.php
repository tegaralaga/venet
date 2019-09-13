<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProvinsi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_provinsi', function (Blueprint $table) {
            $table->integer('pro_id')->autoIncrement();
            $table->integer('pro_neg_id');
            $table->string('pro_name', 150)->nullable(false);
            $table->char('pro_kode', 8);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_provinsi');
    }
}
