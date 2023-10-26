<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGameInstanceSettings extends Migration
{

    public function up()
    {
        Schema::create('game_instance_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('game_instance_id')->unsigned();
            $table->string('key');
            $table->string('value');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('game_instance_settings');
    }
}