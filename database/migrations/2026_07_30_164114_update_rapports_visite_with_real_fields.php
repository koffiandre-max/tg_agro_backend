<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cette migration ciblait l'ancienne table "rapports_visite".
        // Elle est rendue obsolète par la migration
        // 2026_07_30_164114_drop_and_recreate_rapport_visites_table qui recrée
        // la table "rapport_visites" avec tous les champs réels (superficie_visitee_ha,
        // gps_latitude, etc.) et le softDeletes. On la neutralise pour éviter une
        // erreur "table does not exist".
    }

    public function down(): void
    {
        //
    }
};
