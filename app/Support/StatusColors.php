<?php

namespace App\Support;

/**
 * Centralise les classes Tailwind pour les badges de statut.
 *
 * Usage dans les vues Blade :
 *   <span class="{{ \App\Support\StatusColors::badge($invoice->status) }} px-2 py-0.5 text-xs rounded-md font-medium">
 *       {{ $invoice->status }}
 *   </span>
 *
 * Ou via le composant x-ui.badge qui utilise ce mapping automatiquement :
 *   <x-ui.badge status="{{ $invoice->status }}">Payée</x-ui.badge>
 */
class StatusColors
{
    /**
     * Retourne les classes Tailwind pour un badge de statut.
     * Inclut bg, text, et border pour un rendu "pill" avec bordure légère.
     */
    public static function badge(string $status): string
    {
        return match ($status) {
            // Succès — vert émeraude
            'paid', 'active', 'approved', 'posted', 'confirmed', 'completed', 'done'
                => 'bg-emerald-100 text-emerald-700 border border-emerald-200',

            // Attention — ambre
            'pending', 'draft', 'partial', 'on_hold', 'submitted'
                => 'bg-amber-100 text-amber-700 border border-amber-200',

            // Danger — rouge
            'overdue', 'rejected', 'void', 'inactive', 'cancelled', 'expired', 'failed'
                => 'bg-red-100 text-red-700 border border-red-200',

            // Info — bleu
            'sent', 'viewed', 'processing', 'converted', 'in_progress'
                => 'bg-blue-100 text-blue-700 border border-blue-200',

            // Primaire — indigo
            'new', 'open', 'todo'
                => 'bg-indigo-100 text-indigo-700 border border-indigo-200',

            // Défaut
            default => 'bg-gray-100 text-gray-600 border border-gray-200',
        };
    }

    /**
     * Retourne la couleur du point (dot) pour un statut.
     * Utilisé avec les badges dot ou les indicateurs de statut.
     */
    public static function dot(string $status): string
    {
        return match ($status) {
            'paid', 'active', 'approved', 'posted', 'confirmed', 'completed', 'done'
                => 'bg-emerald-500',

            'pending', 'draft', 'partial', 'on_hold', 'submitted'
                => 'bg-amber-500',

            'overdue', 'rejected', 'void', 'inactive', 'cancelled', 'expired', 'failed'
                => 'bg-red-500',

            'sent', 'viewed', 'processing', 'converted', 'in_progress'
                => 'bg-blue-500',

            'new', 'open', 'todo'
                => 'bg-indigo-500',

            default => 'bg-gray-400',
        };
    }

    /**
     * Retourne le label FR par défaut pour un statut (optionnel, à surcharger côté vue).
     */
    public static function label(string $status): string
    {
        return match ($status) {
            'paid'        => 'Payée',
            'partial'     => 'Partielle',
            'pending'     => 'En attente',
            'draft'       => 'Brouillon',
            'submitted'   => 'Soumis',
            'sent'        => 'Envoyée',
            'viewed'      => 'Vue',
            'todo'        => 'À faire',
            'overdue'     => 'En retard',
            'cancelled'   => 'Annulée',
            'void'        => 'Annulée',
            'approved'    => 'Approuvée',
            'rejected'    => 'Rejetée',
            'confirmed'   => 'Confirmée',
            'active'      => 'Actif',
            'inactive'    => 'Inactif',
            'posted'      => 'Validée',
            'converted'   => 'Converti',
            'expired'     => 'Expirée',
            'processing'  => 'En cours',
            'in_progress' => 'En cours',
            'completed'   => 'Terminé',
            'failed'      => 'Échoué',
            default       => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    /**
     * Returns a list of allowed status values for a given domain.
     */
    public static function statuses(string $domain): array
    {
        return match ($domain) {
            'quote' => ['draft', 'sent', 'viewed', 'approved', 'rejected', 'expired', 'converted'],
            'project' => ['planning', 'active', 'on_hold', 'completed', 'cancelled'],
            'task' => ['todo', 'in_progress', 'done', 'cancelled'],
            'expense_claim' => ['draft', 'submitted', 'approved', 'rejected', 'paid'],
            'bill' => ['draft', 'submitted', 'approved', 'paid', 'void', 'cancelled'],
            'purchase_order' => ['draft', 'sent', 'confirmed', 'received', 'partial', 'cancelled', 'closed'],
            'recurring_invoice' => ['draft', 'active', 'paused', 'completed', 'cancelled'],
            'reminder' => ['pending', 'sent', 'failed', 'cancelled'],
            'hr_leave' => ['pending', 'approved', 'rejected', 'cancelled'],
            'invoice' => ['draft', 'sent', 'partial', 'overdue', 'paid', 'cancelled', 'void'],
            'subscription' => ['active', 'pending', 'expired', 'cancelled'],
            'journal_entry' => ['draft', 'posted', 'approved'],
            default => [],
        };
    }

    /**
     * Generate an array suitable for x-custom-select options for a given status domain.
     *
     * Each option includes: value, label (translated) and dot (status color)
     */
    public static function options(string $domain): array
    {
        return array_map(
            fn($status) => [
                'value' => $status,
                'label' => static::label($status),
                'dot'   => static::dot($status),
            ],
            static::statuses($domain)
        );
    }
}
