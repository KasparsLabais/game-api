<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserStatsTable extends Migration
{
    public function up()
    {
        Schema::create('user_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('key');
            $table->string('value');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_stats');
    }
}