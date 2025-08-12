<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            // ذخیره قیمت خرید در زمان فروش
            $table->decimal('cost_price', 10, 2)->default(0)->after('unit_price');
            // اختیاری: ذخیره سود برای هر آیتم (همیشه می‌توان محاسبه کرد)
            $table->decimal('profit', 10, 2)->default(0)->after('total_price');
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'profit']);
        });
    }
};
