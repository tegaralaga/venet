<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTblVenueAddCoordinate extends Migration
{
    private $table_name = 'tbl_venue';
    private $prefix = 'ven_';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
           $table->point($this->prefix . 'coordinate', 0)->nullable(false)->after($this->prefix . 'kel_id');
            $table->spatialIndex($this->prefix . 'coordinate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropSpatialIndex('tbl_venue_ven_coordinate_spatialindex');
            $table->dropColumn($this->prefix . 'coordinate');
        });
    }
}
