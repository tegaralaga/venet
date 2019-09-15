<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEvent extends Migration
{
    private $table_name = 'tbl_event';
    private $prefix = 'eve_';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->bigIncrements($this->prefix . 'id');
            $table->integer($this->prefix . 'ety_id')->default(0)->nullable(false);
            $table->bigInteger($this->prefix . 'ven_id')->default(0)->nullable(false);
            $table->integer($this->prefix . 'org_id')->default(0)->nullable(false);
            $table->string($this->prefix . 'name', 150)->nullable(false);
            $table->text($this->prefix . 'description')->nullable(true);
            $table->date($this->prefix . 'date_start')->nullable(false);
            $table->date($this->prefix . 'date_end')->nullable(false);
            $table->boolean($this->prefix . 'published')->default(false)->nullable(false);
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
