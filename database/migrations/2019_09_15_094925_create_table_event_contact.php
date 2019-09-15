<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEventContact extends Migration
{
    private $table_name = 'tbl_event_contact';
    private $prefix = 'eco_';
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
            $table->enum($this->prefix . 'type', ['PHONE_NUMBER', 'MOBILE_NUMBER', 'FAX_NUMBER', 'EMAIL', 'WEBSITE', 'TWITTER', 'INSTAGRAM', 'FACEBOOK']);
            $table->string($this->prefix . 'value', 200);
            $table->string($this->prefix . 'description', 100)->nullable(true);
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
