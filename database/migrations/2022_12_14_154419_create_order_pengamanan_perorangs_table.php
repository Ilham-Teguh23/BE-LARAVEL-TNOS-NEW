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
        // Schema::create('order_pengamanan_perorangs', function (Blueprint $table) {
        //     // $table->uuid('id')->primary();
        //     // $table->string('tnos_service_id');
        //     // $table->string('external_id')->nullable();
        //     // $table->string('invoice_id')->nullable();
        //     // $table->string('user_id');
        //     // $table->text('needs');
        //     // $table->string('name');
        //     // $table->string('email');
        //     // $table->string('phone');
        //     // $table->dateTime('time');
        //     // $table->integer('duration');
        //     // $table->string('location');
        //     // $table->integer('jml_personil')->default(1);
        //     // $table->integer('order_total')->nullable();
        //     // $table->enum('payment_status', ['0', '1', '2', '3'])->comment('0=masuk ke order,1=menunggu pembayaran, 2=sudah dibayar, 3=kadaluarsa');
        //     // $table->softDeletes();
        //     // $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_pengamanan_perorangs');
    }
};
