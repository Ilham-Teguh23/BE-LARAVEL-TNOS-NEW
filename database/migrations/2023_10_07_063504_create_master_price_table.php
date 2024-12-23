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
        Schema::create('master_price', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->nullable();
            $table->integer('partner_id')->nullable();
            $table->string('layanan'); //
            $table->string('price_user');
            $table->string('price_tnos');
            $table->string('price_client')->nullable();
            $table->string('price_partner')->nullable();
            $table->boolean('is_active');
            $table->boolean('is_client');
            $table->string('klasifikasi')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('master_price');
    }
};
