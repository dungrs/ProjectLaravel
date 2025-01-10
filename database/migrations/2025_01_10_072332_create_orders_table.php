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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20);
            $table->string('fullname');
            $table->string('phone', 20);
            $table->string('email', 50);
            $table->string('province_id', 10)->nullable();
            $table->string('district_id', 10)->nullable();
            $table->string('ward_id', 10)->nullable();
            $table->string('address');
            $table->text('description')->nullable();
            $table->json('promotion')->nullable();
            $table->json('cart')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('guest_cookie')->nullable();
            $table->string('method', 20);
            $table->string('confirm', 20);
            $table->string('payment', 20);
            $table->string('delevery', 20);
            $table->float('shipping')->default(0);
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
