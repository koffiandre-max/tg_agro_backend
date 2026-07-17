<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_prices', function (Blueprint $table) {
            $table->id();
            $table->string('product_name', 100);
            $table->string('unit', 50);
            $table->decimal('price_per_unit', 15, 2);
            $table->string('currency', 10)->default('FCFA');
            $table->string('region', 100)->nullable();
            $table->date('recorded_at');
            $table->string('source', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_prices');
    }
};
