<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Supprimer toutes les tables liées aux rapports de visite (y compris les tables dépendantes)
        Schema::dropIfExists('photos_visite');
        Schema::dropIfExists('validations_rapport');
        Schema::dropIfExists('notifications_visite');
        Schema::dropIfExists('rapport_visite_photos');
        Schema::dropIfExists('notes_rapport');
        Schema::dropIfExists('prochaine_visite');
        Schema::dropIfExists('observations_finales');
        Schema::dropIfExists('performances_elevage');
        Schema::dropIfExists('alimentation_eau');
        Schema::dropIfExists('soins_animaux');
        Schema::dropIfExists('observations_sanitaires');
        Schema::dropIfExists('signes_cliniques');
        Schema::dropIfExists('etats_elevage');
        Schema::dropIfExists('types_animaux');
        Schema::dropIfExists('visite_elevage');
        Schema::dropIfExists('entretien_intrants');
        Schema::dropIfExists('sols_irrigations');
        Schema::dropIfExists('observations_ravageurs');
        Schema::dropIfExists('ravageurs_maladies');
        Schema::dropIfExists('etats_vegetatifs');
        Schema::dropIfExists('types_cultures');
        Schema::dropIfExists('visite_cultures');
        Schema::dropIfExists('rapports_visite');

        // Créer la nouvelle table avec le bon schéma
        Schema::create('rapport_visites', function (Blueprint $table) {
            $table->id();

            // ---------- Étape 1 : Informations générales ----------
            $table->foreignId('technicien_id')->constrained('users')->cascadeOnDelete();
            $table->date('date_visite');
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('localisation_parcelle');

            $table->string('type_visite');

            $table->string('conditions_meteo')->nullable();
            $table->decimal('superficie_visitee_ha', 8, 2)->nullable();

            $table->string('duree_visite')->nullable();

            $table->decimal('gps_latitude', 10, 7)->nullable();
            $table->decimal('gps_longitude', 10, 7)->nullable();

            // ---------- Étape 2 : Cultures ----------
            $table->json('cultures_presentes')->nullable();
            $table->string('culture_autre_precision')->nullable();

            $table->string('stade_phenologique')->nullable();

            $table->unsignedTinyInteger('avancement_cycle_pourcent')->nullable();
            $table->unsignedTinyInteger('etat_couvert_vegetal')->nullable();

            $table->json('ravageurs_maladies')->nullable();
            $table->string('ravageur_autre_precision')->nullable();
            $table->unsignedTinyInteger('niveau_infestation')->nullable();
            $table->text('observations_ravageurs')->nullable();

            $table->string('etat_hydrique_sol')->nullable();
            $table->boolean('irrigation_en_place')->nullable();
            $table->string('etat_structure_sol')->nullable();
            $table->decimal('ph_sol', 3, 1)->nullable();

            $table->json('entretien_intrants')->nullable();
            $table->text('intrants_utilises')->nullable();

            $table->decimal('estimation_recolte_kg', 10, 2)->nullable();
            $table->date('date_estimee_recolte')->nullable();

            // ---------- Étape 3 : Élevage ----------
            $table->json('animaux_presents')->nullable();
            $table->string('animal_autre_precision')->nullable();

            $table->unsignedInteger('effectif_total')->nullable();
            $table->unsignedInteger('mortalite_constatee')->nullable();
            $table->unsignedInteger('naissances_depuis_derniere_visite')->nullable();
            $table->unsignedInteger('ventes_abattages_depuis_derniere_visite')->nullable();
            $table->unsignedTinyInteger('etat_corporel_general')->nullable();

            $table->json('signes_cliniques')->nullable();
            $table->string('signe_autre_precision')->nullable();
            $table->text('observations_sanitaires')->nullable();

            $table->json('soins_traitements')->nullable();
            $table->text('produits_administres')->nullable();

            $table->string('etat_alimentation')->nullable();
            $table->string('eau_abreuvement')->nullable();
            $table->unsignedTinyInteger('etat_batiments_enclos')->nullable();

            $table->decimal('production_laitiere_l_j', 8, 2)->nullable();
            $table->unsignedInteger('production_oeufs_nb_j')->nullable();
            $table->decimal('gain_poids_kg_mois', 8, 2)->nullable();

            // ---------- Étape 4 : Observations finales ----------
            $table->text('resume_visite')->nullable();

            $table->string('niveau_alerte')->default('aucune');
            $table->text('description_alerte')->nullable();

            $table->date('prochaine_visite_date')->nullable();
            $table->string('prochaine_visite_raison')->nullable();

            // ---------- Étape 5 : Résumé & envoi ----------
            $table->text('note_interne')->nullable();
            $table->text('message_client')->nullable();

            $table->string('statut')->default('brouillon');
            $table->text('motif_rejet')->nullable();
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('valide_at')->nullable();
            $table->timestamp('envoye_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Créer la table des photos
        Schema::create('rapport_visite_photos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rapport_visite_id')
                ->constrained('rapport_visites')
                ->cascadeOnDelete();

            $table->string('chemin');
            $table->string('legende')->nullable();

            $table->decimal('gps_latitude', 10, 7)->nullable();
            $table->decimal('gps_longitude', 10, 7)->nullable();
            $table->timestamp('pris_le')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapport_visite_photos');
        Schema::dropIfExists('rapport_visites');
    }
};
