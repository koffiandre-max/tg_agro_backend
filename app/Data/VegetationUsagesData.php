<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class VegetationUsagesData extends Data
{
    public function __construct(
        #[Nullable]
        public ?string $occupation_actuelle = null,

        #[Nullable]
        public ?string $cultures_place = null,

        #[Nullable]
        public ?bool $presence_arbres = null,

        #[Nullable]
        public ?string $commentaire_arbres = null,

        #[Nullable]
        public ?string $especes_ligneuses = null,

        #[Nullable]
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

        #[Nullable]
        public ?string $produits_autre_detail = null,
    ) {}
}
