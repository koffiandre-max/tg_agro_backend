<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table principale des rapports de visite
        Schema::create('rapports_visite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technicien_id')->constrained('users')->onDelete('cascade');
            $table->date('date_visite');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('farm_id')->nullable()->constrained('farms')->onDelete('set null');
            $table->string('localisation_parcelle', 200);
            $table->text('type_visite');
            $table->text('conditions_meteo')->nullable();
            $table->decimal('superficie_visitee', 10, 2)->nullable();
            $table->text('duree_visite')->nullable();
            $table->string('latitude', 20)->nullable();
            $table->string('longitude', 20)->nullable();
            $table->text('statut')->default('Brouillon');
            $table->dateTime('date_envoi')->nullable();
            $table->dateTime('date_validation')->nullable();
            $table->timestamps();
        });

        // Étape 2 - Informations sur les cultures
        Schema::create('visite_cultures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapport_id')->constrained('rapports_visite')->onDelete('cascade');
            $table->timestamps();
        });

        // Types de cultures présentes (cases à cocher)
        Schema::create('types_cultures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visite_cultures_id')->constrained('visite_cultures')->onDelete('cascade');
            $table->text('culture_type');
            $table->string('culture_autre_detail', 100)->nullable();
            $table->boolean('present')->default(false);
            $table->timestamps();
        });

        // État végétatif des cultures
        Schema::create('etats_vegetatifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visite_cultures_id')->constrained('visite_cultures')->onDelete('cascade');
            $table->text('stade_phenologique')->nullable();
            $table->integer('avancement_cycle')->nullable();
            $table->integer('etat_couvert')->nullable();
            $table->timestamps();
        });

        // Ravageurs et maladies observés
        Schema::create('ravageurs_maladies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visite_cultures_id')->constrained('visite_cultures')->onDelete('cascade');
            $table->text('type_probleme');
            $table->string('probleme_autre_detail', 100)->nullable();
            $table->boolean('present')->default(false);
            $table->integer('niveau_infestation')->nullable();
            $table->timestamps();
        });

        // Observations détaillées sur les ravageurs/maladies
        Schema::create('observations_ravageurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visite_cultures_id')->constrained('visite_cultures')->onDelete('cascade');
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        // État du sol et irrigation
        Schema::create('sols_irrigations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visite_cultures_id')->constrained('visite_cultures')->onDelete('cascade');
            $table->text('etat_hydrique_sol')->nullable();
            $table->boolean('irrigation_place')->nullable();
            $table->text('etat_structure_sol')->nullable();
            $table->decimal('ph_sol_mesure', 4, 2)->nullable();
            $table->timestamps();
        });

        // Entretien et intrants
        Schema::create('entretien_intrants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visite_cultures_id')->constrained('visite_cultures')->onDelete('cascade');
            $table->boolean('desherbage_effectue')->default(false);
            $table->boolean('taille_elagage')->default(false);
            $table->boolean('engrais_applique')->default(false);
            $table->boolean('traitement_phytosanitaire')->default(false);
            $table->boolean('mulching_realise')->default(false);
            $table->boolean('compost_apporte')->default(false);
            $table->text('intrants_utilises')->nullable();
            $table->decimal('estimation_recolte', 10, 2)->nullable();
            $table->date('date_estimee_recolte')->nullable();
            $table->timestamps();
        });

        // Étape 3 - Informations sur l'élevage
        Schema::create('visite_elevage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapport_id')->constrained('rapports_visite')->onDelete('cascade');
            $table->timestamps();
        });

        // Types d'animaux présents
        Schema::create('types_animaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visite_elevage_id')->constrained('visite_elevage')->onDelete('cascade');
            $table->text('animal_type');
            $table->string('animal_autre_detail', 100)->nullable();
            $table->boolean('present')->default(false);
            $table->integer('effectif_total')->nullable();
            $table->integer('mortalite_constatee')->default(0);
            $table->integer('naissances_visite')->nullable();
            $table->integer('ventes_abatages')->nullable();
            $table->timestamps();
        });

        // État général de l'élevage
        Schema::create('etats_elevage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visite_elevage_id')->constrained('visite_elevage')->onDelete('cascade');
            $table->integer('etat_corporel')->nullable();
            $table->timestamps();
        });

        // Signes cliniques observés
        Schema::create('signes_cliniques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visite_elevage_id')->constrained('visite_elevage')->onDelete('cascade');
            $table->text('type_signe');
            $table->string('signe_autre_detail', 100)->nullable();
            $table->boolean('present')->default(false);
            $table->timestamps();
        });

        // Observations détaillées sur la santé animale
        Schema::create('observations_sanitaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visite_elevage_id')->constrained('visite_elevage')->onDelete('cascade');
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        // Soins et traitements réalisés
        Schema::create('soins_animaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visite_elevage_id')->constrained('visite_elevage')->onDelete('cascade');
            $table->boolean('vaccination_effectuee')->default(false);
            $table->boolean('deparasitage_interne')->default(false);
            $table->boolean('deparasitage_externe')->default(false);
            $table->boolean('traitement_antibiotique')->default(false);
            $table->boolean('soins_plaies')->default(false);
            $table->boolean('consultation_veterinaire')->default(false);
            $table->text('produits_administres')->nullable();
            $table->timestamps();
        });

        // Alimentation, eau et logement
        Schema::create('alimentation_eau', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visite_elevage_id')->constrained('visite_elevage')->onDelete('cascade');
            $table->text('etat_alimentation')->nullable();
            $table->text('eau_abreuvement')->nullable();
            $table->integer('etat_batiments')->nullable();
            $table->timestamps();
        });

        // Performances productives
        Schema::create('performances_elevage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visite_elevage_id')->constrained('visite_elevage')->onDelete('cascade');
            $table->decimal('production_laitiere', 5, 2)->nullable();
            $table->integer('production_oeufs')->nullable();
            $table->decimal('gain_poids', 5, 2)->nullable();
            $table->timestamps();
        });

        // Observations générales et alertes
        Schema::create('observations_finales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapport_id')->constrained('rapports_visite')->onDelete('cascade');
            $table->text('resume_visite');
            $table->text('niveau_alerte')->nullable();
            $table->text('description_alerte')->nullable();
            $table->timestamps();
        });

        // Prochaine visite recommandée
        Schema::create('prochaine_visite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapport_id')->constrained('rapports_visite')->onDelete('cascade');
            $table->date('date_souhaitee')->nullable();
            $table->text('raison')->nullable();
            $table->timestamps();
        });

        // Étape 5 - Notes et messages
        Schema::create('notes_rapport', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapport_id')->constrained('rapports_visite')->onDelete('cascade');
            $table->text('note_interne')->nullable();
            $table->text('message_client')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
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
    }
};
