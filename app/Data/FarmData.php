<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Date;

class FarmData extends Data
{
    public function __construct(
        // Champs existants
        #[Required, Max(255)]
        public string $name,

        #[Required, Max(255)]
        public string $location,

        #[Nullable, Numeric]
        public ?float $latitude = null,

        #[Nullable, Numeric]
        public ?float $longitude = null,

        #[Required, Numeric]
        public float $total_area_hectares,

        #[Required, Max(255)]
        public string $culture_type,

        #[Required, Max(50)]
        public string $status = 'active',

        #[Nullable, Date]
        public ?string $expected_harvest_date = null,

        #[Nullable, Max(100)]
        public ?string $crop_stage = null,

        #[Nullable, Numeric]
        public ?int $crop_stage_progress = 0,

        #[Nullable, Date]
        public ?string $last_visit_date = null,

        #[Nullable]
        public ?string $notes = null,

        #[Required, Max(255)]
        public string $user_id,

        // Champs de parcelles
        #[Nullable, Max(50)]
        public ?string $reference_dossier = null,

        #[Nullable, Date]
        public ?string $date_declaration = null,

        #[Nullable, Max(100)]
        public ?string $nom_client = null,

        #[Nullable, Max(100)]
        public ?string $contact = null,

        #[Nullable, Max(50)]
        public ?string $numero_cadastral = null,

        // Caractéristiques générales
        #[Nullable, Numeric]
        public ?float $surface_totale = null,

        #[Nullable, Numeric]
        public ?float $surface_cultivable = null,

        #[Nullable, Max(50)]
        public ?string $forme_parcelle = null,

        #[Nullable, Max(50)]
        public ?string $exposition_principale = null,

        #[Nullable, Max(50)]
        public ?string $pente_moyenne = null,

        #[Nullable, Numeric]
        public ?int $altitude = null,

        #[Nullable, Max(50)]
        public ?string $topographie = null,

        // Sols
        #[Nullable, Max(50)]
        public ?string $type_sol = null,

        #[Nullable, Max(50)]
        public ?string $couleur_sol = null,

        #[Nullable, Max(20)]
        public ?string $profondeur_sol = null,

        #[Nullable]
        public ?bool $presence_cailloux = null,

        #[Nullable]
        public ?string $commentaire_cailloux = null,

        #[Nullable]
        public ?bool $problemes_erosion = null,

        #[Nullable]
        public ?string $commentaire_erosion = null,

        #[Nullable]
        public ?bool $analyse_sol_realisee = null,

        #[Nullable]
        public ?string $commentaire_analyse = null,

        #[Nullable, Numeric]
        public ?float $ph = null,

        #[Nullable, Max(100)]
        public ?string $source_ph = null,

        // Ressources en eau
        #[Nullable]
        public ?bool $point_eau_proximite = null,

        #[Nullable]
        public ?string $commentaire_point_eau = null,

        #[Nullable, Max(50)]
        public ?string $type_point_eau = null,

        #[Nullable, Numeric]
        public ?int $distance_point_eau = null,

        #[Nullable]
        public ?bool $systeme_irrigation = null,

        #[Nullable]
        public ?string $commentaire_irrigation = null,

        #[Nullable, Max(50)]
        public ?string $type_irrigation = null,

        #[Nullable]
        public ?bool $inondations_saisonnieres = null,

        #[Nullable]
        public ?string $commentaire_inondations = null,

        #[Nullable, Max(100)]
        public ?string $periode_secheresse = null,

        // Végétation et usages
        #[Nullable, Max(50)]
        public ?string $occupation_actuelle = null,

        #[Nullable, Max(200)]
        public ?string $cultures_place = null,

        #[Nullable]
        public ?bool $presence_arbres = null,

        #[Nullable]
        public ?string $commentaire_arbres = null,

        #[Nullable]
        public ?string $especes_ligneuses = null,

        #[Nullable, Max(50)]
        public ?string $rendement_actuel = null,

        #[Nullable]
        public ?bool $antecedents_traitement = null,

        #[Nullable]
        public ?string $commentaire_traitement = null,

        #[Nullable]
        public ?bool $produits_herbicides = null,

        #[Nullable]
        public ?bool $produits_pesticides = null,

        #[Nullable]
        public ?bool $produits_engrais = null,

        #[Nullable]
        public ?bool $produits_autre = null,

        #[Nullable, Max(100)]
        public ?string $produits_autre_detail = null,

        // Accès et infrastructures
        #[Nullable]
        public ?bool $acces_carrossable = null,

        #[Nullable]
        public ?string $commentaire_acces = null,

        #[Nullable, Numeric]
        public ?float $distance_route_principale = null,

        #[Nullable]
        public ?bool $cloture_existante = null,

        #[Nullable]
        public ?string $commentaire_cloture = null,

        #[Nullable]
        public ?bool $batiment_hangar = null,

        #[Nullable]
        public ?string $commentaire_batiment = null,

        #[Nullable]
        public ?bool $electricite_disponible = null,

        #[Nullable]
        public ?string $commentaire_electricite = null,

        #[Nullable]
        public ?bool $reseau_telephonique = null,

        #[Nullable]
        public ?string $commentaire_reseau = null,

        // Remarques client
        #[Nullable]
        public ?string $observations_libres = null,

        #[Nullable, Date]
        public ?string $signature_date = null,

        #[Nullable]
        public ?string $signature = null,

        // Vérifications - Générales
        #[Nullable, Max(50)]
        public ?string $surface_totale_declare = null,

        #[Nullable, Max(50)]
        public ?string $surface_totale_constate = null,

        #[Nullable]
        public ?bool $surface_totale_concorde = null,

        #[Nullable, Max(50)]
        public ?string $surface_cultivable_declare = null,

        #[Nullable, Max(50)]
        public ?string $surface_cultivable_constate = null,

        #[Nullable]
        public ?bool $surface_cultivable_concorde = null,

        #[Nullable, Max(50)]
        public ?string $exposition_declare = null,

        #[Nullable, Max(50)]
        public ?string $exposition_constate = null,

        #[Nullable]
        public ?bool $exposition_concorde = null,

        #[Nullable, Max(50)]
        public ?string $pente_declare = null,

        #[Nullable, Max(50)]
        public ?string $pente_constate = null,

        #[Nullable]
        public ?bool $pente_concorde = null,

        #[Nullable, Max(50)]
        public ?string $topographie_declare = null,

        #[Nullable, Max(50)]
        public ?string $topographie_constate = null,

        #[Nullable]
        public ?bool $topographie_concorde = null,

        // Vérifications - Sols
        #[Nullable, Max(50)]
        public ?string $type_sol_declare = null,

        #[Nullable, Max(50)]
        public ?string $type_sol_constate = null,

        #[Nullable]
        public ?bool $type_sol_concorde = null,

        #[Nullable, Max(50)]
        public ?string $profondeur_sol_declare = null,

        #[Nullable, Max(50)]
        public ?string $profondeur_sol_constate = null,

        #[Nullable]
        public ?bool $profondeur_sol_concorde = null,

        #[Nullable, Max(50)]
        public ?string $presence_pierres_declare = null,

        #[Nullable, Max(50)]
        public ?string $presence_pierres_constate = null,

        #[Nullable]
        public ?bool $presence_pierres_concorde = null,

        #[Nullable, Max(50)]
        public ?string $erosion_declare = null,

        #[Nullable, Max(50)]
        public ?string $erosion_constate = null,

        #[Nullable]
        public ?bool $erosion_concorde = null,

        #[Nullable, Max(50)]
        public ?string $ph_sol_declare = null,

        #[Nullable, Max(50)]
        public ?string $ph_sol_constate = null,

        #[Nullable]
        public ?bool $ph_sol_concorde = null,

        // Vérifications - Eau
        #[Nullable, Max(50)]
        public ?string $point_eau_declare = null,

        #[Nullable, Max(50)]
        public ?string $point_eau_constate = null,

        #[Nullable]
        public ?bool $point_eau_concorde = null,

        #[Nullable, Max(50)]
        public ?string $type_point_eau_declare = null,

        #[Nullable, Max(50)]
        public ?string $type_point_eau_constate = null,

        #[Nullable]
        public ?bool $type_point_eau_concorde = null,

        #[Nullable, Max(50)]
        public ?string $distance_point_eau_declare = null,

        #[Nullable, Max(50)]
        public ?string $distance_point_eau_constate = null,

        #[Nullable]
        public ?bool $distance_point_eau_concorde = null,

        #[Nullable, Max(50)]
        public ?string $irrigation_declare = null,

        #[Nullable, Max(50)]
        public ?string $irrigation_constate = null,

        #[Nullable]
        public ?bool $irrigation_concorde = null,

        #[Nullable, Max(50)]
        public ?string $inondations_declare = null,

        #[Nullable, Max(50)]
        public ?string $inondations_constate = null,

        #[Nullable]
        public ?bool $inondations_concorde = null,

        // Vérifications - Végétation
        #[Nullable, Max(50)]
        public ?string $occupation_declare = null,

        #[Nullable, Max(50)]
        public ?string $occupation_constate = null,

        #[Nullable]
        public ?bool $occupation_concorde = null,

        #[Nullable, Max(50)]
        public ?string $cultures_declare = null,

        #[Nullable, Max(50)]
        public ?string $cultures_constate = null,

        #[Nullable]
        public ?bool $cultures_concorde = null,

        #[Nullable, Max(50)]
        public ?string $arbres_declare = null,

        #[Nullable, Max(50)]
        public ?string $arbres_constate = null,

        #[Nullable]
        public ?bool $arbres_concorde = null,

        #[Nullable, Max(50)]
        public ?string $traitements_declare = null,

        #[Nullable, Max(50)]
        public ?string $traitements_constate = null,

        #[Nullable]
        public ?bool $traitements_concorde = null,

        // Vérifications - Accès
        #[Nullable, Max(50)]
        public ?string $acces_declare = null,

        #[Nullable, Max(50)]
        public ?string $acces_constate = null,

        #[Nullable]
        public ?bool $acces_concorde = null,

        #[Nullable, Max(50)]
        public ?string $distance_route_declare = null,

        #[Nullable, Max(50)]
        public ?string $distance_route_constate = null,

        #[Nullable]
        public ?bool $distance_route_concorde = null,

        #[Nullable, Max(50)]
        public ?string $cloture_declare = null,

        #[Nullable, Max(50)]
        public ?string $cloture_constate = null,

        #[Nullable]
        public ?bool $cloture_concorde = null,

        #[Nullable, Max(50)]
        public ?string $batiment_declare = null,

        #[Nullable, Max(50)]
        public ?string $batiment_constate = null,

        #[Nullable]
        public ?bool $batiment_concorde = null,

        #[Nullable, Max(50)]
        public ?string $electricite_declare = null,

        #[Nullable, Max(50)]
        public ?string $electricite_constate = null,

        #[Nullable]
        public ?bool $electricite_concorde = null,

        // Synthèse vérification
        #[Nullable, Numeric]
        public ?int $nombre_criteres = null,

        #[Nullable, Numeric]
        public ?int $conformes = null,

        #[Nullable, Numeric]
        public ?int $ecarts = null,

        #[Nullable, Numeric]
        public ?int $total = null,

        #[Nullable]
        public ?string $ecarts_significatifs = null,

        #[Nullable, Max(50)]
        public ?string $recommandation = null,

        #[Nullable, Max(100)]
        public ?string $verificateur_nom = null,

        #[Nullable, Max(100)]
        public ?string $verificateur_poste = null,

        #[Nullable, Date]
        public ?string $verificateur_date = null,

        #[Nullable]
        public ?string $verificateur_signature = null,
    ) {}
}
