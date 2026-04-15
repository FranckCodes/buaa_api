<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreign('id')->references('id')->on('users')->cascadeOnDelete();

            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->enum('sexe', ['M', 'F'])->nullable();
            $table->string('etat_civil')->nullable();

            $table->text('adresse_complete')->nullable();
            $table->string('ville')->nullable();
            $table->string('province')->nullable();
            $table->string('territoire')->nullable();

            $table->foreignId('client_activity_type_id')->nullable()->constrained('client_activity_types')->nullOnDelete();
            $table->foreignId('client_structure_type_id')->nullable()->constrained('client_structure_types')->nullOnDelete();

            $table->string('profession_detaillee')->nullable();
            $table->integer('experience_annees')->default(0);
            $table->decimal('superficie_exploitation', 12, 2)->nullable();
            $table->string('type_culture')->nullable();
            $table->integer('nombre_animaux')->nullable();

            $table->decimal('revenus_mensuels', 12, 2)->nullable();
            $table->text('autres_sources_revenus')->nullable();
            $table->string('banque_principale')->nullable();
            $table->string('numero_compte')->nullable();

            $table->string('ref_nom')->nullable();
            $table->string('ref_telephone')->nullable();
            $table->string('ref_relation')->nullable();

            $table->string('superviseur_id')->nullable();
            $table->foreign('superviseur_id')->references('id')->on('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
