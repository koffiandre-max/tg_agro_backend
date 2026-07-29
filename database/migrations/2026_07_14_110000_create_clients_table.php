<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->string('country_of_residence', 100)->nullable();
            $table->string('country_of_origin', 100)->nullable();
            $table->string('city_of_residence', 100)->nullable();
            $table->string('id_document_type', 50)->nullable();
            $table->string('id_document_number', 100)->nullable();
            $table->text('subscription_type')->default('basic');
            $table->date('subscription_expires_at')->nullable();
            $table->decimal('total_investment', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
