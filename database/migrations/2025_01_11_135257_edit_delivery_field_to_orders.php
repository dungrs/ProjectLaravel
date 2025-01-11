<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Bước 1: Thêm cột mới
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery')->nullable();
        });

        // Bước 2: Sao chép dữ liệu từ 'delevery' sang 'delivery'
        DB::statement('UPDATE orders SET delivery = delevery');

        // Bước 3: Xóa cột cũ
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delevery');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Bước 1: Thêm lại cột 'delevery'
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delevery')->nullable();
        });

        // Bước 2: Sao chép dữ liệu từ 'delivery' về 'delevery'
        DB::statement('UPDATE orders SET delevery = delivery');

        // Bước 3: Xóa cột 'delivery'
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery');
        });
    }
};