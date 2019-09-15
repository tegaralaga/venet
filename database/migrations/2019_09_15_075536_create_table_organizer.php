<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrganizer extends Migration
{
    private $table_name = 'tbl_organizer';
    private $prefix = 'org_';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments($this->prefix . 'id');
            $table->string($this->prefix . 'name', 150)->nullable(false);
            $table->string($this->prefix . 'description', 200)->nullable(true);
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
        Schema::dropIfExists($this->table_name);
    }
}
