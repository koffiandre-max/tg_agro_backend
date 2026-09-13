<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registre des opérations hors-ligne (outbox mobile).
     *
     * Chaque opération locale du mobile possède un `client_uuid` unique
     * qui permet un traitement idempotent : si le mobile retente l'envoi
     * (après perte de réseau, redémarrage…), le serveur ne crée pas de doublon
     * et renvoie l'entité serveur déjà créée.
     */
    public function up(): void
    {
        Schema::create('sync_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('device_id', 100)->nullable();
            $table->string('client_uuid', 64);
            $table->string('entity_type', 100);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('operation', 20);          // created | updated | deleted
            $table->string('status', 20)->default('applied'); // applied | error | skipped
            $table->text('error_message')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'client_uuid']);
            $table->index(['user_id', 'entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_operations');
    }
};