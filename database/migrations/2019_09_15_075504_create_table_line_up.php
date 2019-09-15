<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLineUp extends Migration
{
    private $table_name = 'tbl_line_up';
    private $prefix = 'lin_';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments($this->prefix . 'id');
            $table->integer($this->prefix . 'lty_id')->default(0)->nullable(false);
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
