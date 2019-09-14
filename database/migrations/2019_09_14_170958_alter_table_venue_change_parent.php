<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableVenueChangeParent extends Migration
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
            \DB::statement("ALTER TABLE `tbl_venue` RENAME COLUMN `ven_parent_id` TO `ven_parent`;");
            \DB::statement("ALTER TABLE `tbl_venue` ALTER `ven_parent` SET DEFAULT 0;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
