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
            $table->string('payment_status', 30)->default('pending_payment')->after('status');
            $table->timestamp('last_payment_at')->nullable()->after('payment_status');
            $table->timestamp('next_payment_reminder_at')->nullable()->after('next_payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'last_payment_at',
                'next_payment_reminder_at',
            ]);
        });
    }
};
