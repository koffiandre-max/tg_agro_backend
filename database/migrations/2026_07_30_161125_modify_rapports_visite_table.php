<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rapports_visite', function (Blueprint $table) {
            // Ajouter le champ parcelle_id s'il n'existe pas (sans contrainte de clé étrangère pour l'instant)
            if (!Schema::hasColumn('rapports_visite', 'parcelle_id')) {
                $table->unsignedBigInteger('parcelle_id')->nullable()->after('farm_id');
            }

            // Ajouter des index pour améliorer les performances
            if (!Schema::hasColumn('rapports_visite', 'statut')) {
                $table->index('statut');
            }
            if (!Schema::hasColumn('rapports_visite', 'date_visite')) {
                $table->index('date_visite');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rapports_visite', function (Blueprint $table) {
            // Supprimer le champ parcelle_id
            if (Schema::hasColumn('rapports_visite', 'parcelle_id')) {
                $table->dropColumn('parcelle_id');
            }

            // Supprimer les index
            if (Schema::hasIndex('rapports_visite', 'statut')) {
                $table->dropIndex(['statut']);
            }
            if (Schema::hasIndex('rapports_visite', 'date_visite')) {
                $table->dropIndex(['date_visite']);
            }
        });
    }
};
