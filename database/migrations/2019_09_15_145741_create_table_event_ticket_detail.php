<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEventTicketDetail extends Migration
{
    private $table_name = 'tbl_event_ticket_detail';
    private $prefix = 'etd_';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->bigIncrements($this->prefix . 'id');
            $table->bigInteger($this->prefix . 'eti_id')->nullable(false)->default(0);
            $table->string($this->prefix . 'name',100)->nullable(false)->default('');
            $table->string($this->prefix . 'description', 200)->nullable(true);
            $table->integer($this->prefix . 'price')->nullable(false)->default(0);
            $table->integer($this->prefix . 'quote')->nullable(false)->default(0);
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
