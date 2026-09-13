<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute la date de mise à jour "côté appareil" aux entités créées
     * depuis le mobile. Elle sert à la résolution de conflits (Last-Write-Wins) :
     * si deux appareils modifient la même ligne, celle avec le client_updated_at
     * le plus récent gagne.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('data_entries', 'client_updated_at')) {
            Schema::table('data_entries', function (Blueprint $table) {
                $table->timestamp('client_updated_at')->nullable()->after('updated_at');
            });
        }

        if (! Schema::hasColumn('reports', 'client_updated_at')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->timestamp('client_updated_at')->nullable()->after('updated_at');
            });
        }

        if (! Schema::hasColumn('photos', 'client_updated_at')) {
            Schema::table('photos', function (Blueprint $table) {
                $table->timestamp('client_updated_at')->nullable()->after('updated_at');
            });
        }
    }

    public function down(): void
    {
        Schema::table('data_entries', fn (Blueprint $t) => $t->dropColumn('client_updated_at'));
        Schema::table('reports', fn (Blueprint $t) => $t->dropColumn('client_updated_at'));
        Schema::table('photos', fn (Blueprint $t) => $t->dropColumn('client_updated_at'));
    }
};