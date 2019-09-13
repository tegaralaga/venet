<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableVenue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_venue', function (Blueprint $table) {
            $table->bigIncrements('ven_id');
            $table->bigInteger('ven_parent_id')->nullable(true)->comment('Parent Venue');
            $table->integer('ven_vty_id')->comment('Venue Type');
            $table->enum('ven_location_type', ['INDOOR', 'OUTDOOR', 'SEMI'])->default('SEMI')->comment('Venue Location Type');
            $table->integer('ven_kel_id')->comment('Venue Kelurahan');
            $table->integer('ven_capacity')->comment('Venue Capacity');
            $table->string('ven_address', 200)->default('')->comment('Venue Address');
            $table->string('ven_name', 100)->default('')->comment('Venue Name');
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
        Schema::dropIfExists('tbl_venue');
    }
}
