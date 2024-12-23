<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_verify', function (Blueprint $table) {
            // $table->uuid('id')->primary();
            $table->increments('id');
            $table->string('user_id');
            $table->string('token');
            $table->dateTime('expired_email')->nullable();
            $table->timestamps();
        });
  
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_telepon')->default(0)->after('remember_token');
            $table->boolean('is_email_verified')->default(0)->after('no_telepon');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_verify');
    }
};
