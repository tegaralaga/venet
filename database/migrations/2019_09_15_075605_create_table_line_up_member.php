<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLineUpMember extends Migration
{
    private $table_name = 'tbl_line_up_member';
    private $prefix = 'lme_';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments($this->prefix . 'id');
            $table->integer($this->prefix . 'lin_id')->nullable(false);
            $table->integer($this->prefix . 'belong_to')->nullable(false);
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
