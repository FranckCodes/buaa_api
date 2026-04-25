<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supervisor_zones', function (Blueprint $table) {
            $table->id();

            $table->string('superviseur_id', 50);
            $table->foreign('superviseur_id')->references('id')->on('superviseurs')->cascadeOnDelete();

            // Province — toujours obligatoire (point d'entrée de la hiérarchie)
            $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete();

            // Branche rurale
            $table->foreignId('territoire_id')->nullable()->constrained('territoires')->nullOnDelete();
            $table->foreignId('secteur_id')->nullable()->constrained('secteurs')->nullOnDelete();

            // Branche urbaine
            $table->foreignId('ville_id')->nullable()->constrained('villes')->nullOnDelete();
            $table->foreignId('commune_id')->nullable()->constrained('communes')->nullOnDelete();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Un superviseur ne peut pas avoir deux zones identiques
            $table->unique(
                ['superviseur_id', 'province_id', 'territoire_id', 'secteur_id', 'ville_id', 'commune_id'],
                'supervisor_zones_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisor_zones');
    }
};
