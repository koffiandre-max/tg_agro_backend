<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Offres d'abonnement par défaut du module Payment.
 *
 * Idempotent (updateOrInsert par "code") : ré-exécutable sans doublon ;
 * les prix sont des snapshots modifiables directement en base.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('suite_plans')) {
            return;
        }

        $now = now();

        $plans = [
            [
                'code' => 'gratuit_monthly',
                'name' => 'Gratuit',
                'description' => 'Découverte de la plateforme, sans engagement.',
                'price' => 0,
                'currency' => 'XOF',
                'period_months' => 1,
                'features' => json_encode(['1 utilisateur', 'Fonctions de base']),
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'pro_monthly',
                'name' => 'Pro — Mensuel',
                'description' => 'Toutes les fonctions Pro, facturation mensuelle.',
                'price' => 5000,
                'currency' => 'XOF',
                'period_months' => 1,
                'features' => json_encode(['5 utilisateurs', 'Rapports illimités', 'Support prioritaire']),
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'pro_yearly',
                'name' => 'Pro — Annuel',
                'description' => 'Toutes les fonctions Pro, 2 mois offerts.',
                'price' => 50000,
                'currency' => 'XOF',
                'period_months' => 12,
                'features' => json_encode(['5 utilisateurs', 'Rapports illimités', 'Support prioritaire', '2 mois offerts']),
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'code' => 'entreprise_monthly',
                'name' => 'Entreprise — Mensuel',
                'description' => 'Multi-exploitations et accès API.',
                'price' => 15000,
                'currency' => 'XOF',
                'period_months' => 1,
                'features' => json_encode(['Utilisateurs illimités', 'Multi-exploitations', 'Accès API', 'Support dédié']),
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'code' => 'entreprise_yearly',
                'name' => 'Entreprise — Annuel',
                'description' => 'Multi-exploitations et accès API, 2 mois offerts.',
                'price' => 150000,
                'currency' => 'XOF',
                'period_months' => 12,
                'features' => json_encode(['Utilisateurs illimités', 'Multi-exploitations', 'Accès API', 'Support dédié', '2 mois offerts']),
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('suite_plans')->updateOrInsert(
                ['code' => $plan['code']],
                $plan + ['created_at' => $now, 'updated_at' => $now],
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('suite_plans')) {
            return;
        }

        DB::table('suite_plans')->whereIn('code', [
            'gratuit_monthly',
            'pro_monthly',
            'pro_yearly',
            'entreprise_monthly',
            'entreprise_yearly',
        ])->delete();
    }
};
