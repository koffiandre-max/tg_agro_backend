<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_entries', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('validated_at');
            $table->boolean('seen_by_client')->default(false)->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('data_entries', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'seen_by_client']);
        });
    }
};