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
        Schema::create('pemesanan_fortune', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("b2b_id", 50);
            $table->date("tanggal_pemesanan");
            $table->time("jam_pemesanan");
            $table->string("koordinator_id", 50);
            $table->enum("status", ["1", "0"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pemesanan_fortune');
    }
};
