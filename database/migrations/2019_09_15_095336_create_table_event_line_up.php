<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEventLineUp extends Migration
{
    private $table_name = 'tbl_event_line_up';
    private $prefix = 'eli_';
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
            $table->integer($this->prefix . 'lin_id')->nullable(false)->default(0);
            $table->dateTime($this->prefix . 'created_at')->useCurrent();
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
