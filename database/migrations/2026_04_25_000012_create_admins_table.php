<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->foreign('id')->references('id')->on('users')->cascadeOnDelete();

            // Profil pro
            $table->string('matricule', 50)->unique()->nullable();
            $table->string('telephone_pro', 20)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            // Identité
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance', 191)->nullable();
            $table->enum('sexe', ['M', 'F'])->nullable();
            $table->string('etat_civil', 50)->nullable();
            $table->string('nationalite', 100)->nullable();

            // Localisation personnelle
            $table->text('adresse_complete')->nullable();
            $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->foreignId('territoire_id')->nullable()->constrained('territoires')->nullOnDelete();
            $table->foreignId('secteur_id')->nullable()->constrained('secteurs')->nullOnDelete();
            $table->foreignId('ville_id')->nullable()->constrained('villes')->nullOnDelete();
            $table->foreignId('commune_id')->nullable()->constrained('communes')->nullOnDelete();

            // Professionnel
            $table->string('niveau_etude', 100)->nullable();
            $table->string('specialite', 191)->nullable();
            $table->integer('experience_annees')->default(0);

            // Pièce d'identité
            $table->string('type_piece_identite', 50)->nullable();
            $table->string('numero_piece_identite', 100)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
