<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('superviseurs', function (Blueprint $table) {
            // Identité
            $table->date('date_naissance')->nullable()->after('matricule');
            $table->string('lieu_naissance', 191)->nullable()->after('date_naissance');
            $table->enum('sexe', ['M', 'F'])->nullable()->after('lieu_naissance');
            $table->string('etat_civil', 50)->nullable()->after('sexe');
            $table->string('nationalite', 100)->nullable()->after('etat_civil');

            // Localisation personnelle
            $table->text('adresse_complete')->nullable()->after('nationalite');
            $table->foreignId('province_id')->nullable()->after('adresse_complete')
                ->constrained('provinces')->nullOnDelete();
            $table->foreignId('territoire_id')->nullable()->after('province_id')
                ->constrained('territoires')->nullOnDelete();
            $table->foreignId('secteur_id')->nullable()->after('territoire_id')
                ->constrained('secteurs')->nullOnDelete();
            $table->foreignId('ville_id')->nullable()->after('secteur_id')
                ->constrained('villes')->nullOnDelete();
            $table->foreignId('commune_id')->nullable()->after('ville_id')
                ->constrained('communes')->nullOnDelete();

            // Informations professionnelles
            $table->string('niveau_etude', 100)->nullable()->after('commune_id');
            $table->string('specialite', 191)->nullable()->after('niveau_etude');
            $table->integer('experience_annees')->default(0)->after('specialite');

            // Pièce d'identité
            $table->string('type_piece_identite', 50)->nullable()->after('experience_annees');
            $table->string('numero_piece_identite', 100)->nullable()->after('type_piece_identite');
        });
    }

    public function down(): void
    {
        Schema::table('superviseurs', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropForeign(['territoire_id']);
            $table->dropForeign(['secteur_id']);
            $table->dropForeign(['ville_id']);
            $table->dropForeign(['commune_id']);
            $table->dropColumn([
                'date_naissance', 'lieu_naissance', 'sexe', 'etat_civil', 'nationalite',
                'adresse_complete', 'province_id', 'territoire_id', 'secteur_id', 'ville_id', 'commune_id',
                'niveau_etude', 'specialite', 'experience_annees',
                'type_piece_identite', 'numero_piece_identite',
            ]);
        });
    }
};
