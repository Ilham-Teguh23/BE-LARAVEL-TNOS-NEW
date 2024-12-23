<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('b2b_orders', function (Blueprint $table) {
            $table->string('type')->after("user_id")->default("tnos")->comment('type order client');
            $table->integer('partner_id')->after("invoice_id")->nullable()->comment('partner id tnos');
            $table->integer('pendapatan_partner')->after("pendapatan_mitra")->nullable()->comment('pendapatan partner');
            $table->string('start_lattitude')->after("pendapatan_mitra")->nullable()->comment('partner id tnos');
            $table->string('start_longitude')->after("start_lattitude")->nullable()->comment('partner id tnos');
            $table->string('end_lattitude')->after("start_longitude")->nullable()->comment('partner id tnos');
            $table->string('end_longitude')->after("end_lattitude")->nullable()->comment('partner id tnos');
            $table->string('jarak')->after("end_longitude")->nullable()->comment('partner id tnos');
            $table->string('start_address')->after("jarak")->nullable()->comment('partner id tnos');
            $table->string('end_address')->after("start_address")->nullable()->comment('partner id tnos');
            $table->string('biaya_tekhnical_meeting')->after("end_address")->nullable()->comment('partner id tnos');
            $table->string('biaya_makan')->after("biaya_tekhnical_meeting")->nullable()->comment('partner id tnos');
            $table->string('biaya_transport')->after("biaya_makan")->nullable()->comment('partner id tnos');
            $table->string('status_transaksi')->after("biaya_transport")->nullable()->comment('partner id tnos');
            $table->string('keperluan_pengamanan')->after("status_transaksi")->nullable()->comment('partner id tnos');
            $table->string('nama_pic')->after("keperluan_pengamanan")->nullable()->comment('partner id tnos');
            $table->string('nomor_pic')->after("nama_pic")->nullable()->comment('partner id tnos');
            $table->date('tanggal_mulai')->after("nomor_pic")->nullable()->comment('partner id tnos');
            $table->string('jam_mulai')->after("tanggal_mulai")->nullable()->comment('partner id tnos');
            $table->string('durasi_pengamanan')->after("jam_mulai")->nullable()->comment('partner id tnos');
            $table->string('jumlah_tenaga_pengamanan')->after("durasi_pengamanan")->nullable()->comment('partner id tnos');
            $table->string('jenis_layanan')->after("jumlah_tenaga_pengamanan")->nullable()->comment('partner id tnos');
            $table->string('code')->after("jenis_layanan")->nullable()->comment('partner id tnos');
            $table->string('tracking_status')->after("code")->nullable()->comment('partner id tnos');
            $table->string('biaya_pengamanan')->after("tracking_status")->nullable()->comment('partner id tnos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('b2b_orders', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('partner_id');
            $table->dropColumn('pendapatan_partner');
            $table->dropColumn('start_lattitude');
            $table->dropColumn('start_longitude');
            $table->dropColumn('end_lattitude');
            $table->dropColumn('end_longitude');
            $table->dropColumn('jarak');
            $table->dropColumn('start_address');
            $table->dropColumn('end_address');
            $table->dropColumn('biaya_tekhnical_meeting');
            $table->dropColumn('biaya_makan');
            $table->dropColumn('biaya_transport');
            $table->dropColumn('status_transaksi');
        });
    }
}
