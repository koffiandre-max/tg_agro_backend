<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rapports_visite', function (Blueprint $table) {
            // Supprimer les colonnes qui ne correspondent pas au nouveau schéma
            if (Schema::hasColumn('rapports_visite', 'farm_id')) {
                $table->dropForeign(['farm_id']);
                $table->dropColumn('farm_id');
            }
            if (Schema::hasColumn('rapports_visite', 'parcelle_id')) {
                $table->dropColumn('parcelle_id');
            }
            if (Schema::hasColumn('rapports_visite', 'type_visite')) {
                $table->text('type_visite')->nullable()->change();
            }
            if (Schema::hasColumn('rapports_visite', 'conditions_meteo')) {
                $table->text('conditions_meteo')->nullable()->change();
            }
            if (Schema::hasColumn('rapports_visite', 'superficie_visitee')) {
                $table->dropColumn('superficie_visitee');
            }
            if (Schema::hasColumn('rapports_visite', 'duree_visite')) {
                $table->text('duree_visite')->nullable()->change();
            }
            if (Schema::hasColumn('rapports_visite', 'latitude')) {
                $table->dropColumn('latitude');
            }
            if (Schema::hasColumn('rapports_visite', 'longitude')) {
                $table->dropColumn('longitude');
            }
            if (Schema::hasColumn('rapports_visite', 'date_envoi')) {
                $table->dropColumn('date_envoi');
            }
            if (Schema::hasColumn('rapports_visite', 'date_validation')) {
                $table->dropColumn('date_validation');
            }
        });

        Schema::table('rapports_visite', function (Blueprint $table) {
            // Ajouter les nouvelles colonnes pour Étape 1
            $table->decimal('superficie_visitee_ha', 8, 2)->nullable()->after('localisation_parcelle');
            $table->decimal('gps_latitude', 10, 7)->nullable()->after('superficie_visitee_ha');
            $table->decimal('gps_longitude', 10, 7)->nullable()->after('gps_latitude');

            // Ajouter les colonnes pour Étape 2 : Cultures
            $table->json('cultures_presentes')->nullable()->after('gps_longitude');
            $table->string('culture_autre_precision')->nullable()->after('cultures_presentes');
            $table->enum('stade_phenologique', [
                'germination',
                'croissance_vegetative',
                'floraison',
                'nouaison',
                'fructification',
                'maturation',
                'recolte',
                'post_recolte',
            ])->nullable()->after('culture_autre_precision');
            $table->unsignedTinyInteger('avancement_cycle_pourcent')->nullable()->after('stade_phenologique');
            $table->unsignedTinyInteger('etat_couvert_vegetal')->nullable()->after('avancement_cycle_pourcent');
            $table->json('ravageurs_maladies')->nullable()->after('etat_couvert_vegetal');
            $table->string('ravageur_autre_precision')->nullable()->after('ravageurs_maladies');
            $table->unsignedTinyInteger('niveau_infestation')->nullable()->after('ravageur_autre_precision');
            $table->text('observations_ravageurs')->nullable()->after('niveau_infestation');
            $table->enum('etat_hydrique_sol', ['tres_sec', 'sec', 'humide_normal', 'sature_exces'])->nullable()->after('observations_ravageurs');
            $table->boolean('irrigation_en_place')->nullable()->after('etat_hydrique_sol');
            $table->enum('etat_structure_sol', ['bon_meuble', 'compact', 'erosion_visible', 'croute'])->nullable()->after('irrigation_en_place');
            $table->decimal('ph_sol', 3, 1)->nullable()->after('etat_structure_sol');
            $table->json('entretien_intrants')->nullable()->after('ph_sol');
            $table->text('intrants_utilises')->nullable()->after('entretien_intrants');
            $table->decimal('estimation_recolte_kg', 10, 2)->nullable()->after('intrants_utilises');
            $table->date('date_estimee_recolte')->nullable()->after('estimation_recolte_kg');

            // Ajouter les colonnes pour Étape 3 : Élevage
            $table->json('animaux_presents')->nullable()->after('date_estimee_recolte');
            $table->string('animal_autre_precision')->nullable()->after('animaux_presents');
            $table->unsignedInteger('effectif_total')->nullable()->after('animal_autre_precision');
            $table->unsignedInteger('mortalite_constatee')->nullable()->after('effectif_total');
            $table->unsignedInteger('naissances_depuis_derniere_visite')->nullable()->after('mortalite_constatee');
            $table->unsignedInteger('ventes_abattages_depuis_derniere_visite')->nullable()->after('naissances_depuis_derniere_visite');
            $table->unsignedTinyInteger('etat_corporel_general')->nullable()->after('ventes_abattages_depuis_derniere_visite');
            $table->json('signes_cliniques')->nullable()->after('etat_corporel_general');
            $table->string('signe_autre_precision')->nullable()->after('signes_cliniques');
            $table->text('observations_sanitaires')->nullable()->after('signe_autre_precision');
            $table->json('soins_traitements')->nullable()->after('observations_sanitaires');
            $table->text('produits_administres')->nullable()->after('soins_traitements');
            $table->enum('etat_alimentation', [
                'suffisante_bonne_qualite',
                'suffisante_qualite_moyenne',
                'insuffisante_quantite',
                'insuffisante_qualite',
            ])->nullable()->after('produits_administres');
            $table->enum('eau_abreuvement', [
                'propre_accessible',
                'accessible_trouble',
                'insuffisante',
                'absente',
            ])->nullable()->after('etat_alimentation');
            $table->unsignedTinyInteger('etat_batiments_enclos')->nullable()->after('eau_abreuvement');
            $table->decimal('production_laitiere_l_j', 8, 2)->nullable()->after('etat_batiments_enclos');
            $table->unsignedInteger('production_oeufs_nb_j')->nullable()->after('production_laitiere_l_j');
            $table->decimal('gain_poids_kg_mois', 8, 2)->nullable()->after('production_oeufs_nb_j');

            // Ajouter les colonnes pour Étape 4 : Observations finales
            $table->text('resume_visite')->nullable()->after('gain_poids_kg_mois');
            $table->enum('niveau_alerte', ['aucune', 'faible', 'moderee', 'urgente'])->default('aucune')->after('resume_visite');
            $table->text('description_alerte')->nullable()->after('niveau_alerte');
            $table->date('prochaine_visite_date')->nullable()->after('description_alerte');
            $table->enum('prochaine_visite_raison', [
                'suivi_mensuel',
                'traitement_a_controler',
                'recolte_a_surveiller',
                'urgence_sanitaire',
            ])->nullable()->after('prochaine_visite_date');

            // Ajouter les colonnes pour Étape 5 : Résumé & Envoi
            $table->text('note_interne')->nullable()->after('prochaine_visite_raison');
            $table->text('message_client')->nullable()->after('note_interne');
            $table->text('motif_rejet')->nullable()->after('message_client');
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete()->after('motif_rejet');
            $table->timestamp('valide_at')->nullable()->after('valide_par');
            $table->timestamp('envoye_at')->nullable()->after('valide_at');
            $table->softDeletes()->after('envoye_at');
        });
    }

    public function down(): void
    {
        Schema::table('rapports_visite', function (Blueprint $table) {
            // Supprimer les colonnes ajoutées
            $table->dropSoftDeletes();
            $table->dropColumn([
                'envoye_at',
                'valide_at',
                'valide_par',
                'motif_rejet',
                'message_client',
                'note_interne',
                'prochaine_visite_raison',
                'prochaine_visite_date',
                'description_alerte',
                'niveau_alerte',
                'resume_visite',
                'gain_poids_kg_mois',
                'production_oeufs_nb_j',
                'production_laitiere_l_j',
                'etat_batiments_enclos',
                'eau_abreuvement',
                'etat_alimentation',
                'produits_administres',
                'soins_traitements',
                'observations_sanitaires',
                'signe_autre_precision',
                'signes_cliniques',
                'etat_corporel_general',
                'ventes_abattages_depuis_derniere_visite',
                'naissances_depuis_derniere_visite',
                'mortalite_constatee',
                'effectif_total',
                'animal_autre_precision',
                'animaux_presents',
                'date_estimee_recolte',
                'estimation_recolte_kg',
                'intrants_utilises',
                'entretien_intrants',
                'ph_sol',
                'etat_structure_sol',
                'irrigation_en_place',
                'etat_hydrique_sol',
                'observations_ravageurs',
                'niveau_infestation',
                'ravageur_autre_precision',
                'ravageurs_maladies',
                'etat_couvert_vegetal',
                'avancement_cycle_pourcent',
                'stade_phenologique',
                'culture_autre_precision',
                'cultures_presentes',
                'gps_longitude',
                'gps_latitude',
                'superficie_visitee_ha',
            ]);
        });
    }
};
