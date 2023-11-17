<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('game_instances', function (Blueprint $table) {
            $table->integer('pin')->nullable();
        });
    }

    public function down()
    {
        Schema::table('game_instances', function (Blueprint $table) {
            $table->dropColumn('pin');
        });
    }

};