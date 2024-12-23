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
        Schema::create('provider', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("name_sc", 150);
            $table->string("name_pt", 150);
            $table->string("slug", 255);
            $table->string("image")->nullable();
            $table->text("description");
            $table->enum("status", ["1", "0"]);
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
        Schema::dropIfExists('provider');
    }
};
