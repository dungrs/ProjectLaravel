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
        Schema::table('promotions', function (Blueprint $table) {
            $table->bigInteger('discountValue')->default(0); // Sử dụng bigInteger cho discountValue
            $table->string('discountType', 10); // Kiểu giảm giá (ví dụ: percent hoặc fixed)
            $table->bigInteger('maxDiscountValue')->default(0); // Sử dụng bigInteger cho maxDiscountValue
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn(['discountValue', 'discountType', 'maxDiscountValue']);
        });
    }
};