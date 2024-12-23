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
        Schema::create('fortune', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("nik", 50);
            $table->string("npwp", 50);
            $table->string("nama", 150);
            $table->string("email", 150)->unique();
            $table->string("nomor_hp", 30);
            $table->string("tempat_lahir", 100);
            $table->date("tanggal_lahir");
            $table->enum("jenis_kelamin", ["L", "P"]);
            $table->text("domisili");
            $table->string("provinsi")->nullable();
            $table->string("kab_kota")->nullable();
            $table->string("kecamatan")->nullable();
            $table->string("kelurahan")->nullable();
            $table->date("tanggal_mendaftar");
            $table->time("jam_mendaftar");
            $table->string("pendaftar_id", 50);
            $table->enum("status", [1, 0])->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fortune');
    }
};
