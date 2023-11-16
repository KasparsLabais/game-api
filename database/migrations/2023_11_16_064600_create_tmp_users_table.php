<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmpUsersTable extends Migration
{
    public function up()
    {
        Schema::create('tmp_users', function (Blueprint $table) {
            $table->id();
            $table->string('tmp_user_id');
            $table->string('username')->unique();
            $table->string('token');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tmp_users');
    }
}