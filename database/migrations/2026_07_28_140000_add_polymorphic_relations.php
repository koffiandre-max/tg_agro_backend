<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rendre la table photos polymorphique
        // On ajoute les colonnes morphs, on garde les anciennes colonnes pour compatibilité
        if (!Schema::hasColumn('photos', 'photoable_type')) {
            Schema::table('photos', function (Blueprint $table) {
                $table->nullableMorphs('photoable');
            });

            // Migrer les photos existantes : associer à farm_id via photoable
            // Les photos existantes avaient farm_id, on les lie à Farm
            DB::statement("UPDATE photos SET photoable_type = 'App\\Models\\Farm', photoable_id = farm_id WHERE farm_id IS NOT NULL");
        }

        // 2. Créer la table polymorphique des validations
        if (!Schema::hasTable('validations')) {
            Schema::create('validations', function (Blueprint $table) {
                $table->id();
                $table->morphs('validable');
                $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
                $table->text('action');
                $table->text('motif')->nullable();
                $table->dateTime('date_action')->useCurrent();
                $table->timestamps();
            });
        }

        // 3. Créer la table polymorphique des notifications
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('utilisateur_id')->constrained('users')->onDelete('cascade');
                $table->morphs('notifiable');
                $table->string('type_notification', 100);
                $table->text('message');
                $table->string('lien', 500)->nullable();
                $table->boolean('lue')->default(false);
                $table->dateTime('date_creation')->useCurrent();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('validations');

        Schema::table('photos', function (Blueprint $table) {
            $table->dropMorphs('photoable');
        });
    }
};
