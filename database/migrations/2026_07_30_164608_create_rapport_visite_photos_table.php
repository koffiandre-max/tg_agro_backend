<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapport_visite_photos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rapport_visite_id')
                ->constrained('rapports_visite')
                ->cascadeOnDelete();

            $table->string('chemin');            // chemin/path du fichier stocké
            $table->string('legende')->nullable();

            $table->decimal('gps_latitude', 10, 7)->nullable();
            $table->decimal('gps_longitude', 10, 7)->nullable();
            $table->timestamp('pris_le')->nullable(); // horodatage de la prise de photo

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapport_visite_photos');
    }
};
