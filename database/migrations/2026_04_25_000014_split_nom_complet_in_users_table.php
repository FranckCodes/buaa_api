<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nom', 100)->nullable()->after('id');
            $table->string('postnom', 100)->nullable()->after('nom');
            $table->string('prenom', 100)->nullable()->after('postnom');
        });

        // Migrer les données existantes : nom_complet → nom (on met tout dans nom)
        DB::statement("UPDATE users SET nom = nom_complet WHERE nom_complet IS NOT NULL");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nom_complet');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nom_complet')->nullable()->after('id');
        });

        DB::statement("UPDATE users SET nom_complet = CONCAT_WS(' ', nom, postnom, prenom) WHERE nom IS NOT NULL");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nom', 'postnom', 'prenom']);
        });
    }
};
