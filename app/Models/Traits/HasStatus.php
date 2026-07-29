<?php

namespace App\Models\Traits;

trait HasStatus
{
    /**
     * Scope pour filtrer par statut.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where($this->getStatusColumn(), $status);
    }

    /**
     * Vérifie si l'entité peut être soumise pour validation.
     */
    public function estSoumetable(): bool
    {
        return in_array($this->{$this->getStatusColumn()}, $this->getSoumetableStatuses());
    }

    /**
     * Vérifie si l'entité peut être validée.
     */
    public function estValidable(): bool
    {
        return in_array($this->{$this->getStatusColumn()}, $this->getValidableStatuses());
    }

    /**
     * Vérifie si l'entité est rejetée.
     */
    public function estRejetee(): bool
    {
        return $this->{$this->getStatusColumn()} === $this->getRejeteStatus();
    }

    /**
     * Vérifie si l'entité est validée.
     */
    public function estValidee(): bool
    {
        return $this->{$this->getStatusColumn()} === $this->getValideStatus();
    }

    /**
     * Statuts permettant la soumission.
     */
    protected function getSoumetableStatuses(): array
    {
        return ['Brouillon'];
    }

    /**
     * Statuts permettant la validation.
     */
    protected function getValidableStatuses(): array
    {
        return ['En attente de validation'];
    }

    /**
     * Statut "validé".
     */
    protected function getValideStatus(): string
    {
        return 'Validé';
    }

    /**
     * Statut "rejeté".
     */
    protected function getRejeteStatus(): string
    {
        return 'Rejeté';
    }

    /**
     * Nom de la colonne statut.
     */
    protected function getStatusColumn(): string
    {
        return 'statut';
    }
}