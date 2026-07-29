<?php

namespace App\Contracts;

interface SectionVisiteInterface
{
    /**
     * Valide les données de la section.
     * Retourne un tableau des erreurs (vide si valide).
     */
    public function valider(): array;

    /**
     * Convertit la section en tableau pour stockage JSON.
     */
    public function toArray(): array;

    /**
     * Hydrate la section à partir d'un tableau.
     */
    public static function fromArray(array $data): self;
}