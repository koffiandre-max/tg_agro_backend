<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            // Champs de la table parcelles
            $table->string('reference_dossier', 50)->nullable()->after('notes');
            $table->date('date_declaration')->nullable()->after('reference_dossier');
            $table->string('nom_client', 100)->nullable()->after('date_declaration');
            $table->string('contact', 100)->nullable()->after('nom_client');
            $table->string('numero_cadastral', 50)->nullable()->after('longitude');

            // Champs de caracteristiques_generales
            $table->decimal('surface_totale', 10, 2)->nullable()->after('numero_cadastral');
            $table->decimal('surface_cultivable', 10, 2)->nullable()->after('surface_totale');
            $table->string('forme_parcelle', 50)->nullable()->after('surface_cultivable');
            $table->string('exposition_principale', 50)->nullable()->after('forme_parcelle');
            $table->string('pente_moyenne', 50)->nullable()->after('exposition_principale');
            $table->integer('altitude')->nullable()->after('pente_moyenne');
            $table->string('topographie', 50)->nullable()->after('altitude');

            // Champs de sols
            $table->string('type_sol', 50)->nullable()->after('topographie');
            $table->string('couleur_sol', 50)->nullable()->after('type_sol');
            $table->string('profondeur_sol', 20)->nullable()->after('couleur_sol');
            $table->boolean('presence_cailloux')->nullable()->after('profondeur_sol');
            $table->text('commentaire_cailloux')->nullable()->after('presence_cailloux');
            $table->boolean('problemes_erosion')->nullable()->after('commentaire_cailloux');
            $table->text('commentaire_erosion')->nullable()->after('problemes_erosion');
            $table->boolean('analyse_sol_realisee')->nullable()->after('commentaire_erosion');
            $table->text('commentaire_analyse')->nullable()->after('analyse_sol_realisee');
            $table->decimal('ph', 4, 2)->nullable()->after('commentaire_analyse');
            $table->string('source_ph', 100)->nullable()->after('ph');

            // Champs de ressources_eau
            $table->boolean('point_eau_proximite')->nullable()->after('source_ph');
            $table->text('commentaire_point_eau')->nullable()->after('point_eau_proximite');
            $table->string('type_point_eau', 50)->nullable()->after('commentaire_point_eau');
            $table->integer('distance_point_eau')->nullable()->after('type_point_eau');
            $table->boolean('systeme_irrigation')->nullable()->after('distance_point_eau');
            $table->text('commentaire_irrigation')->nullable()->after('systeme_irrigation');
            $table->string('type_irrigation', 50)->nullable()->after('commentaire_irrigation');
            $table->boolean('inondations_saisonnieres')->nullable()->after('type_irrigation');
            $table->text('commentaire_inondations')->nullable()->after('inondations_saisonnieres');
            $table->string('periode_secheresse', 100)->nullable()->after('commentaire_inondations');

            // Champs de vegetation_usages
            $table->string('occupation_actuelle', 50)->nullable()->after('periode_secheresse');
            $table->string('cultures_place', 200)->nullable()->after('occupation_actuelle');
            $table->boolean('presence_arbres')->nullable()->after('cultures_place');
            $table->text('commentaire_arbres')->nullable()->after('presence_arbres');
            $table->text('especes_ligneuses')->nullable()->after('commentaire_arbres');
            $table->string('rendement_actuel', 50)->nullable()->after('especes_ligneuses');
            $table->boolean('antecedents_traitement')->nullable()->after('rendement_actuel');
            $table->text('commentaire_traitement')->nullable()->after('antecedents_traitement');
            $table->boolean('produits_herbicides')->nullable()->after('commentaire_traitement');
            $table->boolean('produits_pesticides')->nullable()->after('produits_herbicides');
            $table->boolean('produits_engrais')->nullable()->after('produits_pesticides');
            $table->boolean('produits_autre')->nullable()->after('produits_engrais');
            $table->string('produits_autre_detail', 100)->nullable()->after('produits_autre');

            // Champs de acces_infrastructures
            $table->boolean('acces_carrossable')->nullable()->after('produits_autre_detail');
            $table->text('commentaire_acces')->nullable()->after('acces_carrossable');
            $table->decimal('distance_route_principale', 5, 2)->nullable()->after('commentaire_acces');
            $table->boolean('cloture_existante')->nullable()->after('distance_route_principale');
            $table->text('commentaire_cloture')->nullable()->after('cloture_existante');
            $table->boolean('batiment_hangar')->nullable()->after('commentaire_cloture');
            $table->text('commentaire_batiment')->nullable()->after('batiment_hangar');
            $table->boolean('electricite_disponible')->nullable()->after('commentaire_batiment');
            $table->text('commentaire_electricite')->nullable()->after('electricite_disponible');
            $table->boolean('reseau_telephonique')->nullable()->after('commentaire_electricite');
            $table->text('commentaire_reseau')->nullable()->after('reseau_telephonique');

            // Champs de remarques_client
            $table->text('observations_libres')->nullable()->after('commentaire_reseau');
            $table->date('signature_date')->nullable()->after('observations_libres');
            $table->text('signature')->nullable()->after('signature_date');

            // Champs de verification_generales
            $table->string('surface_totale_declare', 50)->nullable()->after('signature');
            $table->string('surface_totale_constate', 50)->nullable()->after('surface_totale_declare');
            $table->boolean('surface_totale_concorde')->nullable()->after('surface_totale_constate');
            $table->string('surface_cultivable_declare', 50)->nullable()->after('surface_totale_concorde');
            $table->string('surface_cultivable_constate', 50)->nullable()->after('surface_cultivable_declare');
            $table->boolean('surface_cultivable_concorde')->nullable()->after('surface_cultivable_constate');
            $table->string('exposition_declare', 50)->nullable()->after('surface_cultivable_concorde');
            $table->string('exposition_constate', 50)->nullable()->after('exposition_declare');
            $table->boolean('exposition_concorde')->nullable()->after('exposition_constate');
            $table->string('pente_declare', 50)->nullable()->after('exposition_concorde');
            $table->string('pente_constate', 50)->nullable()->after('pente_declare');
            $table->boolean('pente_concorde')->nullable()->after('pente_constate');
            $table->string('topographie_declare', 50)->nullable()->after('pente_concorde');
            $table->string('topographie_constate', 50)->nullable()->after('topographie_declare');
            $table->boolean('topographie_concorde')->nullable()->after('topographie_constate');

            // Champs de verification_sols
            $table->string('type_sol_declare', 50)->nullable()->after('topographie_concorde');
            $table->string('type_sol_constate', 50)->nullable()->after('type_sol_declare');
            $table->boolean('type_sol_concorde')->nullable()->after('type_sol_constate');
            $table->string('profondeur_sol_declare', 50)->nullable()->after('type_sol_concorde');
            $table->string('profondeur_sol_constate', 50)->nullable()->after('profondeur_sol_declare');
            $table->boolean('profondeur_sol_concorde')->nullable()->after('profondeur_sol_constate');
            $table->string('presence_pierres_declare', 50)->nullable()->after('profondeur_sol_concorde');
            $table->string('presence_pierres_constate', 50)->nullable()->after('presence_pierres_declare');
            $table->boolean('presence_pierres_concorde')->nullable()->after('presence_pierres_constate');
            $table->string('erosion_declare', 50)->nullable()->after('presence_pierres_concorde');
            $table->string('erosion_constate', 50)->nullable()->after('erosion_declare');
            $table->boolean('erosion_concorde')->nullable()->after('erosion_constate');
            $table->string('ph_sol_declare', 50)->nullable()->after('erosion_concorde');
            $table->string('ph_sol_constate', 50)->nullable()->after('ph_sol_declare');
            $table->boolean('ph_sol_concorde')->nullable()->after('ph_sol_constate');

            // Champs de verification_eau
            $table->string('point_eau_declare', 50)->nullable()->after('ph_sol_concorde');
            $table->string('point_eau_constate', 50)->nullable()->after('point_eau_declare');
            $table->boolean('point_eau_concorde')->nullable()->after('point_eau_constate');
            $table->string('type_point_eau_declare', 50)->nullable()->after('point_eau_concorde');
            $table->string('type_point_eau_constate', 50)->nullable()->after('type_point_eau_declare');
            $table->boolean('type_point_eau_concorde')->nullable()->after('type_point_eau_constate');
            $table->string('distance_point_eau_declare', 50)->nullable()->after('type_point_eau_concorde');
            $table->string('distance_point_eau_constate', 50)->nullable()->after('distance_point_eau_declare');
            $table->boolean('distance_point_eau_concorde')->nullable()->after('distance_point_eau_constate');
            $table->string('irrigation_declare', 50)->nullable()->after('distance_point_eau_concorde');
            $table->string('irrigation_constate', 50)->nullable()->after('irrigation_declare');
            $table->boolean('irrigation_concorde')->nullable()->after('irrigation_constate');
            $table->string('inondations_declare', 50)->nullable()->after('irrigation_concorde');
            $table->string('inondations_constate', 50)->nullable()->after('inondations_declare');
            $table->boolean('inondations_concorde')->nullable()->after('inondations_constate');

            // Champs de verification_vegetation
            $table->string('occupation_declare', 50)->nullable()->after('inondations_concorde');
            $table->string('occupation_constate', 50)->nullable()->after('occupation_declare');
            $table->boolean('occupation_concorde')->nullable()->after('occupation_constate');
            $table->string('cultures_declare', 50)->nullable()->after('occupation_concorde');
            $table->string('cultures_constate', 50)->nullable()->after('cultures_declare');
            $table->boolean('cultures_concorde')->nullable()->after('cultures_constate');
            $table->string('arbres_declare', 50)->nullable()->after('cultures_concorde');
            $table->string('arbres_constate', 50)->nullable()->after('arbres_declare');
            $table->boolean('arbres_concorde')->nullable()->after('arbres_constate');
            $table->string('traitements_declare', 50)->nullable()->after('arbres_concorde');
            $table->string('traitements_constate', 50)->nullable()->after('traitements_declare');
            $table->boolean('traitements_concorde')->nullable()->after('traitements_constate');

            // Champs de verification_acces
            $table->string('acces_declare', 50)->nullable()->after('traitements_concorde');
            $table->string('acces_constate', 50)->nullable()->after('acces_declare');
            $table->boolean('acces_concorde')->nullable()->after('acces_constate');
            $table->string('distance_route_declare', 50)->nullable()->after('acces_concorde');
            $table->string('distance_route_constate', 50)->nullable()->after('distance_route_declare');
            $table->boolean('distance_route_concorde')->nullable()->after('distance_route_constate');
            $table->string('cloture_declare', 50)->nullable()->after('distance_route_concorde');
            $table->string('cloture_constate', 50)->nullable()->after('cloture_declare');
            $table->boolean('cloture_concorde')->nullable()->after('cloture_constate');
            $table->string('batiment_declare', 50)->nullable()->after('cloture_concorde');
            $table->string('batiment_constate', 50)->nullable()->after('batiment_declare');
            $table->boolean('batiment_concorde')->nullable()->after('batiment_constate');
            $table->string('electricite_declare', 50)->nullable()->after('batiment_concorde');
            $table->string('electricite_constate', 50)->nullable()->after('electricite_declare');
            $table->boolean('electricite_concorde')->nullable()->after('electricite_constate');

            // Champs de syntheses_verification
            $table->integer('nombre_criteres')->nullable()->after('electricite_concorde');
            $table->integer('conformes')->nullable()->after('nombre_criteres');
            $table->integer('ecarts')->nullable()->after('conformes');
            $table->integer('total')->nullable()->after('ecarts');
            $table->text('ecarts_significatifs')->nullable()->after('total');
            $table->string('recommandation', 50)->nullable()->after('ecarts_significatifs');
            $table->string('verificateur_nom', 100)->nullable()->after('recommandation');
            $table->string('verificateur_poste', 100)->nullable()->after('verificateur_nom');
            $table->date('verificateur_date')->nullable()->after('verificateur_poste');
            $table->text('verificateur_signature')->nullable()->after('verificateur_date');
        });
    }

    public function down(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->dropColumn([
                'reference_dossier',
                'date_declaration',
                'nom_client',
                'contact',
                'numero_cadastral',
                'surface_totale',
                'surface_cultivable',
                'forme_parcelle',
                'exposition_principale',
                'pente_moyenne',
                'altitude',
                'topographie',
                'type_sol',
                'couleur_sol',
                'profondeur_sol',
                'presence_cailloux',
                'commentaire_cailloux',
                'problemes_erosion',
                'commentaire_erosion',
                'analyse_sol_realisee',
                'commentaire_analyse',
                'ph',
                'source_ph',
                'point_eau_proximite',
                'commentaire_point_eau',
                'type_point_eau',
                'distance_point_eau',
                'systeme_irrigation',
                'commentaire_irrigation',
                'type_irrigation',
                'inondations_saisonnieres',
                'commentaire_inondations',
                'periode_secheresse',
                'occupation_actuelle',
                'cultures_place',
                'presence_arbres',
                'commentaire_arbres',
                'especes_ligneuses',
                'rendement_actuel',
                'antecedents_traitement',
                'commentaire_traitement',
                'produits_herbicides',
                'produits_pesticides',
                'produits_engrais',
                'produits_autre',
                'produits_autre_detail',
                'acces_carrossable',
                'commentaire_acces',
                'distance_route_principale',
                'cloture_existante',
                'commentaire_cloture',
                'batiment_hangar',
                'commentaire_batiment',
                'electricite_disponible',
                'commentaire_electricite',
                'reseau_telephonique',
                'commentaire_reseau',
                'observations_libres',
                'signature_date',
                'signature',
                'surface_totale_declare',
                'surface_totale_constate',
                'surface_totale_concorde',
                'surface_cultivable_declare',
                'surface_cultivable_constate',
                'surface_cultivable_concorde',
                'exposition_declare',
                'exposition_constate',
                'exposition_concorde',
                'pente_declare',
                'pente_constate',
                'pente_concorde',
                'topographie_declare',
                'topographie_constate',
                'topographie_concorde',
                'type_sol_declare',
                'type_sol_constate',
                'type_sol_concorde',
                'profondeur_sol_declare',
                'profondeur_sol_constate',
                'profondeur_sol_concorde',
                'presence_pierres_declare',
                'presence_pierres_constate',
                'presence_pierres_concorde',
                'erosion_declare',
                'erosion_constate',
                'erosion_concorde',
                'ph_sol_declare',
                'ph_sol_constate',
                'ph_sol_concorde',
                'point_eau_declare',
                'point_eau_constate',
                'point_eau_concorde',
                'type_point_eau_declare',
                'type_point_eau_constate',
                'type_point_eau_concorde',
                'distance_point_eau_declare',
                'distance_point_eau_constate',
                'distance_point_eau_concorde',
                'irrigation_declare',
                'irrigation_constate',
                'irrigation_concorde',
                'inondations_declare',
                'inondations_constate',
                'inondations_concorde',
                'occupation_declare',
                'occupation_constate',
                'occupation_concorde',
                'cultures_declare',
                'cultures_constate',
                'cultures_concorde',
                'arbres_declare',
                'arbres_constate',
                'arbres_concorde',
                'traitements_declare',
                'traitements_constate',
                'traitements_concorde',
                'acces_declare',
                'acces_constate',
                'acces_concorde',
                'distance_route_declare',
                'distance_route_constate',
                'distance_route_concorde',
                'cloture_declare',
                'cloture_constate',
                'cloture_concorde',
                'batiment_declare',
                'batiment_constate',
                'batiment_concorde',
                'electricite_declare',
                'electricite_constate',
                'electricite_concorde',
                'nombre_criteres',
                'conformes',
                'ecarts',
                'total',
                'ecarts_significatifs',
                'recommandation',
                'verificateur_nom',
                'verificateur_poste',
                'verificateur_date',
                'verificateur_signature',
            ]);
        });
    }
};
