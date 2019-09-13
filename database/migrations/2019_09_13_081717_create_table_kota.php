<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableKota extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kota', function (Blueprint $table) {
            $table->integer('kot_id')->autoIncrement();
            $table->integer('kot_pro_id');
            $table->string('kot_name', 150)->nullable(false);
            $table->char('kot_kode', 8);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_kota');
    }
}
