<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEventSchedule extends Migration
{
    private $table_name = 'tbl_event_schedule';
    private $prefix = 'esc_';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->bigIncrements($this->prefix . 'id');
            $table->bigInteger($this->prefix . 'eve_id');
            $table->date($this->prefix . 'date_start')->nullable(false);
            $table->date($this->prefix . 'date_end')->nullable(false);
            $table->time($this->prefix . 'time_start')->nullable(true)->default('00:00:00');
            $table->time($this->prefix . 'time_end')->nullable(true)->default('23:59:59');
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
