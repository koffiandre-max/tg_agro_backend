<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('payment_code', 100)->nullable()->after('payment_reference');
            $table->string('payment_provider', 100)->nullable()->after('payment_code');
            $table->string('payment_phone', 20)->nullable()->after('payment_provider');
            $table->json('payment_info')->nullable()->after('payment_phone');
            $table->boolean('auto_payment')->default(false)->after('auto_renew');
            $table->date('next_payment_date')->nullable()->after('auto_payment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'payment_code',
                'payment_provider',
                'payment_phone',
                'payment_info',
                'auto_payment',
                'next_payment_date',
            ]);
        });
    }
};
