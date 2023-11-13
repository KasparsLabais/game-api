<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameCurrencyTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('game_currency', function (Blueprint $table) {
            $table->id();

            $table->string('type')->default('game_played');
            $table->bigInteger('amount')->default(0);
            $table->unsignedBigInteger('user_id');
            $table->bigInteger('game_instance_id')->default(0);
            $table->bigInteger('reference_id')->default(0);
            $table->string('reference_type')->default('')->nullable();
            $table->string('note')->default('')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('game_currency');
    }
};
