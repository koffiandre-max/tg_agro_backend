<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tables contractuelles de la plateforme Business Suite.
 *
 * N'importe quelle app Laravel hôte peut exécuter cette migration pour
 * satisfaire les contrats attendus par les modules SDK (sans les modifier) :
 *   - "businesses" : table tenant référencée par les foreign keys des modules.
 *   - "features"   : catalogue des fonctionnalités payantes/gratuites (feature gate).
 *
 * Idempotente : ne crée une table que si elle n'existe pas encore
 * (compatible avec une vraie plateforme qui les possède déjà).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('businesses')) {
            Schema::create('businesses', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->nullable()->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('features')) {
            Schema::create('features', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('label');
                $table->string('type')->default('paid');
                $table->unsignedBigInteger('amount')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('features');
        Schema::dropIfExists('businesses');
    }
};