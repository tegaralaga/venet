<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEventTicket extends Migration
{
    private $table_name = 'tbl_event_ticket';
    private $prefix = 'eti_';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->bigIncrements($this->prefix . 'id');
            $table->bigInteger($this->prefix . 'eve_id')->nullable(false)->default(0);
            $table->string($this->prefix . 'name', 100)->nullable(false)->default('');
            $table->string($this->prefix . 'description', 200)->nullable(true);
            $table->dateTime($this->prefix . 'date_start')->nullable(false)->useCurrent();
            $table->dateTime($this->prefix . 'date_end')->nullable(false)->useCurrent();
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
