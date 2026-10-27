<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->date('manufacuring_date_create')->nullable()->after('manufacturing_cost');
            $table->date('manufacuring_date_expire')->nullable()->after('manufacuring_date_create');
        });

        Schema::table('currencies', function (Blueprint $table) {
            $table->date('exchange_rate_date')->nullable()->after('exchange_rate');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['manufacuring_date_create', 'manufacuring_date_expire']);
        });

        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn('exchange_rate_date');
        });
    }
};
