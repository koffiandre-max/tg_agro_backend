<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Ajouter la colonne type_activite à rapport_visites
        Schema::table('rapport_visites', function (Blueprint $table) {
            $table->string('type_activite')->default('culture')->after('type_visite');
        });

        // 2) Enrichir la table visite_cultures avec les champs culture
        Schema::table('visite_cultures', function (Blueprint $table) {
            $table->json('cultures_presentes')->nullable()->after('rapport_id');
            $table->string('culture_autre_precision')->nullable()->after('cultures_presentes');
            $table->string('stade_phenologique')->nullable()->after('culture_autre_precision');
            $table->unsignedTinyInteger('avancement_cycle_pourcent')->nullable()->after('stade_phenologique');
            $table->unsignedTinyInteger('etat_couvert_vegetal')->nullable()->after('avancement_cycle_pourcent');
            $table->json('ravageurs_maladies')->nullable()->after('etat_couvert_vegetal');
            $table->string('ravageur_autre_precision')->nullable()->after('ravageurs_maladies');
            $table->unsignedTinyInteger('niveau_infestation')->nullable()->after('ravageur_autre_precision');
            $table->text('observations_ravageurs')->nullable()->after('niveau_infestation');
            $table->string('etat_hydrique_sol')->nullable()->after('observations_ravageurs');
            $table->boolean('irrigation_en_place')->nullable()->after('etat_hydrique_sol');
            $table->string('etat_structure_sol')->nullable()->after('irrigation_en_place');
            $table->decimal('ph_sol', 3, 1)->nullable()->after('etat_structure_sol');
            $table->json('entretien_intrants')->nullable()->after('ph_sol');
            $table->text('intrants_utilises')->nullable()->after('entretien_intrants');
            $table->decimal('estimation_recolte_kg', 10, 2)->nullable()->after('intrants_utilises');
            $table->date('date_estimee_recolte')->nullable()->after('estimation_recolte_kg');
        });

        // 3) Enrichir la table visite_elevage avec les champs élevage
        Schema::table('visite_elevage', function (Blueprint $table) {
            $table->json('animaux_presents')->nullable()->after('rapport_id');
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
            $table->string('etat_alimentation')->nullable()->after('produits_administres');
            $table->string('eau_abreuvement')->nullable()->after('etat_alimentation');
            $table->unsignedTinyInteger('etat_batiments_enclos')->nullable()->after('eau_abreuvement');
            $table->decimal('production_laitiere_l_j', 8, 2)->nullable()->after('etat_batiments_enclos');
            $table->unsignedInteger('production_oeufs_nb_j')->nullable()->after('production_laitiere_l_j');
            $table->decimal('gain_poids_kg_mois', 8, 2)->nullable()->after('production_oeufs_nb_j');
        });

        // 4) Créer la table visite_autres pour les visites de type "autre"
        Schema::create('visite_autres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapport_id')->constrained('rapport_visites')->onDelete('cascade');
            $table->string('type_autre')->nullable();
            $table->text('description_activite')->nullable();
            $table->text('observations_specifiques')->nullable();
            $table->timestamps();
        });

        // 5) Migrer les données existantes de rapport_visites vers les tables annexes
        $rapports = DB::table('rapport_visites')->get();

        foreach ($rapports as $rapport) {
            // Déterminer le type d'activité en fonction des données présentes
            $hasCultureData = !empty($rapport->cultures_presentes)
                || !empty($rapport->stade_phenologique)
                || !empty($rapport->etat_couvert_vegetal)
                || !empty($rapport->ravageurs_maladies)
                || !empty($rapport->etat_hydrique_sol)
                || !empty($rapport->entretien_intrants)
                || !empty($rapport->estimation_recolte_kg);

            $hasElevageData = !empty($rapport->animaux_presents)
                || !empty($rapport->effectif_total)
                || !empty($rapport->etat_corporel_general)
                || !empty($rapport->signes_cliniques)
                || !empty($rapport->soins_traitements)
                || !empty($rapport->etat_alimentation)
                || !empty($rapport->production_laitiere_l_j);

            $typeActivite = $hasCultureData ? 'culture' : ($hasElevageData ? 'elevage' : 'autre');

            // Mettre à jour le type_activite
            DB::table('rapport_visites')
                ->where('id', $rapport->id)
                ->update(['type_activite' => $typeActivite]);

            // Migrer les données culture vers visite_cultures
            if ($hasCultureData) {
                $existingVisiteCulture = DB::table('visite_cultures')
                    ->where('rapport_id', $rapport->id)
                    ->first();

                $cultureData = [
                    'cultures_presentes' => $rapport->cultures_presentes,
                    'culture_autre_precision' => $rapport->culture_autre_precision,
                    'stade_phenologique' => $rapport->stade_phenologique,
                    'avancement_cycle_pourcent' => $rapport->avancement_cycle_pourcent,
                    'etat_couvert_vegetal' => $rapport->etat_couvert_vegetal,
                    'ravageurs_maladies' => $rapport->ravageurs_maladies,
                    'ravageur_autre_precision' => $rapport->ravageur_autre_precision,
                    'niveau_infestation' => $rapport->niveau_infestation,
                    'observations_ravageurs' => $rapport->observations_ravageurs,
                    'etat_hydrique_sol' => $rapport->etat_hydrique_sol,
                    'irrigation_en_place' => $rapport->irrigation_en_place,
                    'etat_structure_sol' => $rapport->etat_structure_sol,
                    'ph_sol' => $rapport->ph_sol,
                    'entretien_intrants' => $rapport->entretien_intrants,
                    'intrants_utilises' => $rapport->intrants_utilises,
                    'estimation_recolte_kg' => $rapport->estimation_recolte_kg,
                    'date_estimee_recolte' => $rapport->date_estimee_recolte,
                    'updated_at' => now(),
                ];

                if ($existingVisiteCulture) {
                    DB::table('visite_cultures')
                        ->where('rapport_id', $rapport->id)
                        ->update($cultureData);
                } else {
                    $cultureData['rapport_id'] = $rapport->id;
                    $cultureData['created_at'] = now();
                    DB::table('visite_cultures')->insert($cultureData);
                }
            }

            // Migrer les données élevage vers visite_elevage
            if ($hasElevageData) {
                $existingVisiteElevage = DB::table('visite_elevage')
                    ->where('rapport_id', $rapport->id)
                    ->first();

                $elevageData = [
                    'animaux_presents' => $rapport->animaux_presents,
                    'animal_autre_precision' => $rapport->animal_autre_precision,
                    'effectif_total' => $rapport->effectif_total,
                    'mortalite_constatee' => $rapport->mortalite_constatee,
                    'naissances_depuis_derniere_visite' => $rapport->naissances_depuis_derniere_visite,
                    'ventes_abattages_depuis_derniere_visite' => $rapport->ventes_abattages_depuis_derniere_visite,
                    'etat_corporel_general' => $rapport->etat_corporel_general,
                    'signes_cliniques' => $rapport->signes_cliniques,
                    'signe_autre_precision' => $rapport->signe_autre_precision,
                    'observations_sanitaires' => $rapport->observations_sanitaires,
                    'soins_traitements' => $rapport->soins_traitements,
                    'produits_administres' => $rapport->produits_administres,
                    'etat_alimentation' => $rapport->etat_alimentation,
                    'eau_abreuvement' => $rapport->eau_abreuvement,
                    'etat_batiments_enclos' => $rapport->etat_batiments_enclos,
                    'production_laitiere_l_j' => $rapport->production_laitiere_l_j,
                    'production_oeufs_nb_j' => $rapport->production_oeufs_nb_j,
                    'gain_poids_kg_mois' => $rapport->gain_poids_kg_mois,
                    'updated_at' => now(),
                ];

                if ($existingVisiteElevage) {
                    DB::table('visite_elevage')
                        ->where('rapport_id', $rapport->id)
                        ->update($elevageData);
                } else {
                    $elevageData['rapport_id'] = $rapport->id;
                    $elevageData['created_at'] = now();
                    DB::table('visite_elevage')->insert($elevageData);
                }
            }
        }

        // 6) Supprimer les champs culture/élevage de la table rapport_visites
        Schema::table('rapport_visites', function (Blueprint $table) {
            $table->dropColumn([
                'cultures_presentes',
                'culture_autre_precision',
                'stade_phenologique',
                'avancement_cycle_pourcent',
                'etat_couvert_vegetal',
                'ravageurs_maladies',
                'ravageur_autre_precision',
                'niveau_infestation',
                'observations_ravageurs',
                'etat_hydrique_sol',
                'irrigation_en_place',
                'etat_structure_sol',
                'ph_sol',
                'entretien_intrants',
                'intrants_utilises',
                'estimation_recolte_kg',
                'date_estimee_recolte',
                'animaux_presents',
                'animal_autre_precision',
                'effectif_total',
                'mortalite_constatee',
                'naissances_depuis_derniere_visite',
                'ventes_abattages_depuis_derniere_visite',
                'etat_corporel_general',
                'signes_cliniques',
                'signe_autre_precision',
                'observations_sanitaires',
                'soins_traitements',
                'produits_administres',
                'etat_alimentation',
                'eau_abreuvement',
                'etat_batiments_enclos',
                'production_laitiere_l_j',
                'production_oeufs_nb_j',
                'gain_poids_kg_mois',
            ]);
        });
    }

    public function down(): void
    {
        // Restaurer les champs culture/élevage dans rapport_visites
        Schema::table('rapport_visites', function (Blueprint $table) {
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
        });

        // Migrer les données de retour
        $visiteCultures = DB::table('visite_cultures')->get();
        foreach ($visiteCultures as $vc) {
            DB::table('rapport_visites')->where('id', $vc->rapport_id)->update([
                'cultures_presentes' => $vc->cultures_presentes,
                'culture_autre_precision' => $vc->culture_autre_precision,
                'stade_phenologique' => $vc->stade_phenologique,
                'avancement_cycle_pourcent' => $vc->avancement_cycle_pourcent,
                'etat_couvert_vegetal' => $vc->etat_couvert_vegetal,
                'ravageurs_maladies' => $vc->ravageurs_maladies,
                'ravageur_autre_precision' => $vc->ravageur_autre_precision,
                'niveau_infestation' => $vc->niveau_infestation,
                'observations_ravageurs' => $vc->observations_ravageurs,
                'etat_hydrique_sol' => $vc->etat_hydrique_sol,
                'irrigation_en_place' => $vc->irrigation_en_place,
                'etat_structure_sol' => $vc->etat_structure_sol,
                'ph_sol' => $vc->ph_sol,
                'entretien_intrants' => $vc->entretien_intrants,
                'intrants_utilises' => $vc->intrants_utilises,
                'estimation_recolte_kg' => $vc->estimation_recolte_kg,
                'date_estimee_recolte' => $vc->date_estimee_recolte,
            ]);
        }

        $visiteElevages = DB::table('visite_elevage')->get();
        foreach ($visiteElevages as $ve) {
            DB::table('rapport_visites')->where('id', $ve->rapport_id)->update([
                'animaux_presents' => $ve->animaux_presents,
                'animal_autre_precision' => $ve->animal_autre_precision,
                'effectif_total' => $ve->effectif_total,
                'mortalite_constatee' => $ve->mortalite_constatee,
                'naissances_depuis_derniere_visite' => $ve->naissances_depuis_derniere_visite,
                'ventes_abattages_depuis_derniere_visite' => $ve->ventes_abattages_depuis_derniere_visite,
                'etat_corporel_general' => $ve->etat_corporel_general,
                'signes_cliniques' => $ve->signes_cliniques,
                'signe_autre_precision' => $ve->signe_autre_precision,
                'observations_sanitaires' => $ve->observations_sanitaires,
                'soins_traitements' => $ve->soins_traitements,
                'produits_administres' => $ve->produits_administres,
                'etat_alimentation' => $ve->etat_alimentation,
                'eau_abreuvement' => $ve->eau_abreuvement,
                'etat_batiments_enclos' => $ve->etat_batiments_enclos,
                'production_laitiere_l_j' => $ve->production_laitiere_l_j,
                'production_oeufs_nb_j' => $ve->production_oeufs_nb_j,
                'gain_poids_kg_mois' => $ve->gain_poids_kg_mois,
            ]);
        }

        // Supprimer les champs ajoutés aux tables annexes
        Schema::table('visite_cultures', function (Blueprint $table) {
            $table->dropColumn([
                'cultures_presentes',
                'culture_autre_precision',
                'stade_phenologique',
                'avancement_cycle_pourcent',
                'etat_couvert_vegetal',
                'ravageurs_maladies',
                'ravageur_autre_precision',
                'niveau_infestation',
                'observations_ravageurs',
                'etat_hydrique_sol',
                'irrigation_en_place',
                'etat_structure_sol',
                'ph_sol',
                'entretien_intrants',
                'intrants_utilises',
                'estimation_recolte_kg',
                'date_estimee_recolte',
            ]);
        });

        Schema::table('visite_elevage', function (Blueprint $table) {
            $table->dropColumn([
                'animaux_presents',
                'animal_autre_precision',
                'effectif_total',
                'mortalite_constatee',
                'naissances_depuis_derniere_visite',
                'ventes_abattages_depuis_derniere_visite',
                'etat_corporel_general',
                'signes_cliniques',
                'signe_autre_precision',
                'observations_sanitaires',
                'soins_traitements',
                'produits_administres',
                'etat_alimentation',
                'eau_abreuvement',
                'etat_batiments_enclos',
                'production_laitiere_l_j',
                'production_oeufs_nb_j',
                'gain_poids_kg_mois',
            ]);
        });

        // Supprimer la table visite_autres
        Schema::dropIfExists('visite_autres');

        // Supprimer la colonne type_activite
        Schema::table('rapport_visites', function (Blueprint $table) {
            $table->dropColumn('type_activite');
        });
    }
};
