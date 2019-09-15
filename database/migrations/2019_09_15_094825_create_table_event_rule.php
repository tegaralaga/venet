<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEventRule extends Migration
{
    private $table_name = 'tbl_event_rule';
    private $prefix = 'eru_';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments($this->prefix . 'id');
            $table->string($this->prefix . 'text_id', 200)->nullable(false)->default('');
            $table->string($this->prefix . 'text_en', 200)->nullable(true)->default('');
            $table->boolean($this->prefix . 'general')->nullable(false)->default(false);
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
