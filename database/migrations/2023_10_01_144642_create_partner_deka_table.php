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
        Schema::create('partner_deka', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('komisi_value_partner');
            $table->string('komisi_percent_partner');
            $table->string('deka_percent');
            $table->string('deka_value');
            $table->string('tnos_percent');
            $table->string('tnos_value');
            $table->string('value_user');
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
        Schema::dropIfExists('partner_deka');
    }
};
