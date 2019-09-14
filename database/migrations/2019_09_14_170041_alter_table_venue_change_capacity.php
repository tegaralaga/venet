<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableVenueChangeCapacity extends Migration
{
    private $table_name = 'tbl_venue';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            \DB::statement("ALTER TABLE `tbl_venue` MODIFY `ven_capacity` INT NULL;");
            \DB::statement("ALTER TABLE `tbl_venue` ALTER `ven_capacity` SET DEFAULT 0;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
