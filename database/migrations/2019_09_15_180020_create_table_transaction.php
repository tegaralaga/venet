<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTransaction extends Migration
{
    private $table_name = 'tbl_transaction';
    private $prefix = 'tra_';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->bigIncrements($this->prefix . 'id');
            $table->integer($this->prefix . 'cus_id')->nullable(false)->default(0);
            $table->string($this->prefix . 'comment', 200)->nullable(true);
            $table->enum($this->prefix . 'status', ['PENDING','WAITING_ON_PAYMENT', 'SUCCESS'])->default('PENDING');
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
