<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTransactionDetailTicket extends Migration
{
    private $table_name = 'tbl_transaction_detail_ticket';
    private $prefix = 'ttd_';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->bigIncrements($this->prefix . 'id');
            $table->bigInteger($this->prefix . 'tde_id');
            $table->boolean($this->prefix . 'checked_in')->nullable(false)->default(false);
            $table->datetime($this->prefix . 'check_in_datetime')->nullable(true);
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
