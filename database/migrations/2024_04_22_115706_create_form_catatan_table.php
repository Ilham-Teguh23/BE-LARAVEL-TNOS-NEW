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
        Schema::create('form_catatan', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("b2b_id", 50);
            $table->string("koordinator_id", 50);
            $table->enum("kondisi_pengamanan", [1, 2, 3]);
            $table->text("catatan")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_catatan');
    }
};
