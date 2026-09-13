<?php

namespace App\Http\Controllers;

use App\Core\ModuleSDK;
use Illuminate\Routing\Controller as Base;
use Illuminate\Support\Facades\Auth;

/**
 * Classe hôte étendue par les contrôleurs des modules Business Suite.
 *
 * Fournit l'accès au tenant courant (business_id) ainsi qu'aux services
 * communs exposés par ModuleSDK, sans que les modules aient à dépendre
 * d'une implémentation particulière.
 */
abstract class BaseController extends Base
{
    /**
     * Identifiant du business (tenant) courant.
     *
     * Retourne l'identifiant du business lié à l'utilisateur authentifié,
     * ou null si aucun tenant n'est résolu (ex: flux public).
     *
     * @return int|null
     */
    protected function getBusinessId(): ?int
    {
        return ModuleSDK::businessId();
    }

    /**
     * Accès au business (tenant) courant.
     */
    protected function business()
    {
        return ModuleSDK::business();
    }
}
