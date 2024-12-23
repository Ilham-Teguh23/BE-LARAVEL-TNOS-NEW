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
        Schema::create('price_pub', function (Blueprint $table) {
            $table->id();
            $table->string('mitra');
            $table->string('code');
            $table->string('personil');
            $table->string('hours')->nullable();
            $table->string('hours_schedule')->nullable();
            $table->string('total_day')->nullable();
            $table->string('tnos_percent');
            $table->string('mitra_percent');
            $table->string('tnos_value');
            $table->string('mitra_value');
            $table->string('price');
            $table->string('is_active');
            $table->string('free_radius');
            $table->string('price_meeting');
            $table->string('price_launch');
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
        Schema::dropIfExists('price_pub');
    }
};
