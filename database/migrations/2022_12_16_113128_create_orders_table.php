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
        if (!Schema::hasTable('b2b_orders')) {
            Schema::create('b2b_orders', function (Blueprint $table) {
                $table->uuid("id")->primary();
                $table->string('tnos_invoice_id')->nullable();
                $table->string('tnos_service_id');
                $table->string('tnos_subservice_id');
                $table->string('external_id')->nullable();
                $table->string('invoice_id')->nullable();
                $table->string('user_id');
                $table->text('needs')->nullable();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->dateTime('time')->nullable();
                $table->integer('duration')->nullable();
                $table->string('location')->nullable();
                $table->string('klasifikasi')->nullable();
                $table->integer('jml_personil')->nullable();
                $table->text('file_document')->nullable();
                $table->string('name_badan_hukum')->nullable();
                $table->integer('modal_dasar')->nullable();
                $table->integer('modal_disetor')->nullable();
                $table->text('alamat_badan_hukum')->nullable();
                $table->text('pemegang_saham')->nullable();
                $table->text('susunan_direksi')->nullable();
                $table->text('bidang_usaha')->nullable();
                $table->string('email_badan_hukum')->nullable();
                $table->string('phone_badan_hukum')->nullable();
                $table->enum('waktu_kerja', ['1', '2'])->nullable()->comment('1=normal shift, 2=night shift');
                $table->integer('order_total')->nullable();
                $table->integer('pendapatan_tnos')->nullable();
                $table->integer('pendapatan_mitra')->nullable();
                $table->enum('status_order',['WAIT','START','RUN', 'AKTA', 'SKMENKUMHAM', 'NPWP', 'NIB', 'WORK', 'FINISH'])->comment('Wait=pesanan sedang diproses crm, Start=Pesanan sudah diproses crm menunggu mitra memulai, Run=Mitra sudah memulai pekerjaanya, Akta=Proses Pembuatan Akta, SKMENKUMHAM=Proses SKMENKUMHAM, NPWP=Proses NPWP, NIB=Proses NIB, Work=Sedang Bertugas, Finish=Pesanan sudah selesai');
                $table->enum('payment_status', ['ORDER','UNPAID', 'PAID', 'SETTLED', 'EXPIRED'])->comment('Order=Memesan tapi belum masuk ke menu pembayaran, Unpaid=Tautan pembayaran sudah berhasil dibuat dan dapat dibayarkan oleh Pelanggan Anda sampai tanggal kedaluwarsa yang Anda tentukan, Paid=Tautan pembayaran sudah berhasil dibayarkan oleh pelanggan Anda, Settled=Dana sudah berhasil diteruskan ke akun Xendit Anda dan dapat ditarik melalui tab Saldo, Expired=kadaluarsa')->default('ORDER');
                $table->string('payment_method')->nullable();
                $table->string('payment_channel')->nullable();
                $table->integer('paid_amount')->nullable();
                $table->string('paid_at')->nullable();
                $table->string('expiry_date')->nullable();
                $table->enum("biaya_survey", ["Ya", "Tidak"])->default("Tidak");
                $table->softDeletes();
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('b2b_orders');
    }
};
