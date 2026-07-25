<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->boolean('is_validated')->default(false)->after('is_visible_to_client');
            $table->dateTime('validated_at')->nullable()->after('is_validated');
        });
    }

    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn(['is_validated', 'validated_at']);
        });
    }
};
