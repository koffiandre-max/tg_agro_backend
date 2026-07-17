<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('clients')->onDelete('cascade');
            $table->string('name', 255);
            $table->string('location', 255);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('total_area_hectares', 10, 2);
            $table->string('culture_type', 100)->nullable();
            $table->enum('status', ['active', 'inactive', 'fallow'])->default('active');
            $table->date('expected_harvest_date')->nullable();
            $table->string('crop_stage', 100)->nullable();
            $table->integer('crop_stage_progress')->default(0);
            $table->date('last_visit_date')->nullable();
            $table->unsignedBigInteger('assigned_technician_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('assigned_technician_id')->references('id')->on('technicians')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};
