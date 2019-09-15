<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTransactionDetail extends Migration
{
    private $table_name = 'tbl_transaction_detail';
    private $prefix = 'tde_';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->bigIncrements($this->prefix . 'id');
            $table->bigInteger($this->prefix . 'tra_id');
            $table->bigInteger($this->prefix . 'etd_id');
            $table->integer($this->prefix . 'price')->nullable(false);
            $table->tinyInteger($this->prefix . 'tax')->nullable(false);
            $table->tinyInteger($this->prefix . 'quantity')->nullable(false);
            $table->integer($this->prefix . 'total')->nullable(false);
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
