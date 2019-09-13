<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTblVenueAddDescription extends Migration
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
            $table->string($this->prefix . 'description', 200)->after($this->prefix . 'name')->nullable(true);
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
            $table->dropColumn($this->prefix . 'description');
        });
    }
}
