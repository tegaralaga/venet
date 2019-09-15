<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCustomer extends Migration
{
    private $table_name = 'tbl_customer';
    private $prefix = 'cus_';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments($this->prefix . 'id');
            $table->integer($this->prefix . 'kel_id')->nullable(true)->default(0);
            $table->string($this->prefix . 'name', 100)->nullable(false)->default('');
            $table->enum($this->prefix . 'gender', ['MALE', 'FEMALE', 'NONE'])->default('NONE');
            $table->enum($this->prefix . 'relationship', ['MARRIED', 'SINGLE', 'NONE'])->default('NONE');
            $table->date($this->prefix . 'birthday')->nullable(true);
            $table->string($this->prefix . 'address', 200)->nullable(true);
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
