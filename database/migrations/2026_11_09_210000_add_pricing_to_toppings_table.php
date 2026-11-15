<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('toppings', function (Blueprint $table) {
            $table->decimal('small_price', 8, 2)->default(1.25)->after('sort_order');
            $table->decimal('medium_price', 8, 2)->default(1.75)->after('small_price');
            $table->decimal('large_price', 8, 2)->default(2.50)->after('medium_price');
        });
    }

    public function down(): void
    {
        Schema::table('toppings', function (Blueprint $table) {
            $table->dropColumn(['small_price', 'medium_price', 'large_price']);
        });
    }
};
