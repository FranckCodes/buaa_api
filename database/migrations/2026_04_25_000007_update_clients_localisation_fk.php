<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Supprimer les anciennes colonnes string
            $table->dropColumn(['ville', 'province', 'territoire']);
        });

        Schema::table('clients', function (Blueprint $table) {
            // Ajouter les FK vers les nouvelles tables
            $table->foreignId('province_id')
                ->nullable()
                ->after('adresse_complete')
                ->constrained('provinces')
                ->nullOnDelete();

            $table->foreignId('territoire_id')
                ->nullable()
                ->after('province_id')
                ->constrained('territoires')
                ->nullOnDelete();

            $table->foreignId('secteur_id')
                ->nullable()
                ->after('territoire_id')
                ->constrained('secteurs')
                ->nullOnDelete();

            $table->foreignId('ville_id')
                ->nullable()
                ->after('secteur_id')
                ->constrained('villes')
                ->nullOnDelete();

            $table->foreignId('commune_id')
                ->nullable()
                ->after('ville_id')
                ->constrained('communes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropForeign(['territoire_id']);
            $table->dropForeign(['secteur_id']);
            $table->dropForeign(['ville_id']);
            $table->dropForeign(['commune_id']);
            $table->dropColumn(['province_id', 'territoire_id', 'secteur_id', 'ville_id', 'commune_id']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->string('ville')->nullable();
            $table->string('province')->nullable();
            $table->string('territoire')->nullable();
        });
    }
};
