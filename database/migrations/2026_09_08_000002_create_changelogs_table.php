<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Journal de changements (architecture "changelog") utilisé par l'application
     * mobile pour synchroniser ses données localement (créations, mises à jour,
     * suppressions) via un curseur incrémental.
     */
    public function up(): void
    {
        Schema::create('changelogs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 100);
            $table->unsignedBigInteger('entity_id');
            $table->string('operation', 20); // created | updated | deleted | restored
            $table->json('payload')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['entity_type', 'entity_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('changelogs');
    }
};