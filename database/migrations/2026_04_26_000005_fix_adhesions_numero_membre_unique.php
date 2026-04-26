<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('adhesions', function (Blueprint $table) {
            // Le numéro de membre doit être unique PAR union, pas globalement
            // (cf. doc Unions & Adhésions v3, §6 "Activation du membre")
            $table->dropUnique('adhesions_numero_membre_unique');
            $table->unique(['union_id', 'numero_membre'], 'adhesions_union_numero_unique');
        });
    }

    public function down(): void
    {
        Schema::table('adhesions', function (Blueprint $table) {
            $table->dropUnique('adhesions_union_numero_unique');
            $table->unique('numero_membre', 'adhesions_numero_membre_unique');
        });
    }
};
