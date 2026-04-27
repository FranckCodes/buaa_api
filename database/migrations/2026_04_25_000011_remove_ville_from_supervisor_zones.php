<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('supervisor_zones', 'ville_id')) {
            return; // Colonne inexistante, rien à faire
        }

        Schema::table('supervisor_zones', function (Blueprint $table) {
            $table->dropForeign(['ville_id']);
        });

        Schema::table('supervisor_zones', function (Blueprint $table) {
            $table->dropColumn('ville_id');
        });
    }

    public function down(): void
    {
        Schema::table('supervisor_zones', function (Blueprint $table) {
            $table->foreignId('ville_id')->nullable()->constrained('villes')->nullOnDelete();
        });
    }
};
