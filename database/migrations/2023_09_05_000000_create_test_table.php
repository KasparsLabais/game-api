<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestTable extends Migration
{

    public function up()
    {
        Schema::create('test', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('test', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test');
    }

}