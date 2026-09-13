<?php

namespace App\Core\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Trait hôte fournissant le mécanisme d'enregistrement utilisé
 * par les ServiceProviders des modules Business Suite.
 *
 * Il est découplé : si la table hôte ("features") n'existe pas,
 * le module s'enregistre quand même (aucune erreur fatale).
 */
trait RegistersModule
{
    /**
     * Enregistre un module (manifest, navigation, rôles, feature).
     *
     * @param string      $manifestPath
     * @param string|null $navigationPath
     * @param string|null $rolesPath
     * @return void
     */
    protected function registerModule(
        string $manifestPath,
        ?string $navigationPath = null,
        ?string $rolesPath = null,
    ): void {
        // 1. Manifest (module.json)
        if (is_file($manifestPath)) {
            try {
                $manifest = json_decode(file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                $manifest = [];
            }

            config([
                'suite.manifest.' . data_get($manifest, 'alias', basename(dirname($manifestPath))) => $manifest,
            ]);
        }

        // 2. Navigation
        if ($navigationPath && is_file($navigationPath)) {
            $navigation = require $navigationPath;
            if (is_array($navigation)) {
                $key = 'suite.navigation';
                config([$key => array_merge((array) config($key, []), $navigation)]);
            }
        }

        // 3. Rôles / permissions
        if ($rolesPath && is_file($rolesPath)) {
            $roles = require $rolesPath;
            if (is_array($roles)) {
                $key = 'suite.roles';
                config([$key => array_merge_recursive((array) config($key, []), $roles)]);
            }
        }

        // 4. Feature (table hôte optionnelle)
        $manifest = config('suite.manifest', []);
        $last     = is_array(end($manifest)) ? end($manifest) : [];

        $code   = data_get($last, 'feature_code');
        $label  = data_get($last, 'feature_label', data_get($last, 'name'));
        $type   = data_get($last, 'type', 'free');
        $amount = (int) data_get($last, 'amount', 0);

        if ($code && Schema::hasTable('features')) {
            try {
                DB::table('features')->updateOrInsert(
                    ['code' => $code],
                    [
                        'label'      => $label,
                        'type'       => $type,
                        'amount'     => $amount,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            } catch (\Throwable $e) {
                // Table non disponible : on ignore silencieusement.
            }
        }
    }
}
