<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEventTicketQuoteHistory extends Migration
{
    private $table_name = 'tbl_event_ticket_quote_history';
    private $prefix = 'eth_';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->bigIncrements($this->prefix . 'id');
            $table->bigInteger($this->prefix . 'etd_id')->nullable(false)->default(0);
            $table->enum($this->prefix . 'type', ['DEBET', 'KREDIT'])->nullable(false)->default('KREDIT');
            $table->enum($this->prefix . 'note', ['INITIAL', 'TRANSACTION', 'INCREASE', 'DECREASE'])->nullable(false)->default('TRANSACTION');
            $table->integer($this->prefix . 'value')->nullable(false)->default(0);
            $table->integer($this->prefix . 'total')->nullable(false)->default(0);
            $table->string($this->prefix . 'comment')->nullable(true);
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
