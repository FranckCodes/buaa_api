<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Drop legacy string-based geo columns
        Schema::table('unions', function (Blueprint $table) {
            $table->dropColumn(['province', 'ville']);
        });

        // 2) Add workflow + normalized geo + president FK
        Schema::table('unions', function (Blueprint $table) {
            // Statut du cycle d'activation (SUSPENDU par défaut, voir UnionStatusSeeder)
            $table->foreignId('union_status_id')
                ->nullable()
                ->after('type')
                ->constrained('union_statuses')
                ->nullOnDelete();

            // Président = user de confiance (relais terrain BUAA)
            // Anciennement string : on remplace par une vraie FK
            $table->dropColumn('president');
        });

        Schema::table('unions', function (Blueprint $table) {
            $table->string('president_id', 50)
                ->nullable()
                ->after('union_status_id');

            $table->foreign('president_id')
                ->references('id')->on('users')
                ->nullOnDelete();

            // Hiérarchie géographique normalisée (cf. clients)
            $table->foreignId('province_id')
                ->nullable()
                ->after('president_id')
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

            // Audit de la validation Admin (passage SUSPENDU -> ACTIVE)
            $table->string('validated_by', 50)->nullable()->after('commune_id');
            $table->foreign('validated_by')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->timestamp('validated_at')->nullable()->after('validated_by');

            // Désactivation définitive (Super Admin uniquement)
            $table->string('deactivated_by', 50)->nullable()->after('validated_at');
            $table->foreign('deactivated_by')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->timestamp('deactivated_at')->nullable()->after('deactivated_by');
            $table->text('deactivation_reason')->nullable()->after('deactivated_at');
        });
    }

    public function down(): void
    {
        Schema::table('unions', function (Blueprint $table) {
            $table->dropForeign(['union_status_id']);
            $table->dropForeign(['president_id']);
            $table->dropForeign(['province_id']);
            $table->dropForeign(['territoire_id']);
            $table->dropForeign(['secteur_id']);
            $table->dropForeign(['ville_id']);
            $table->dropForeign(['commune_id']);
            $table->dropForeign(['validated_by']);
            $table->dropForeign(['deactivated_by']);

            $table->dropColumn([
                'union_status_id',
                'president_id',
                'province_id',
                'territoire_id',
                'secteur_id',
                'ville_id',
                'commune_id',
                'validated_by',
                'validated_at',
                'deactivated_by',
                'deactivated_at',
                'deactivation_reason',
            ]);
        });

        Schema::table('unions', function (Blueprint $table) {
            $table->string('president')->nullable()->after('date_creation');
            $table->string('province')->nullable();
            $table->string('ville')->nullable();
        });
    }
};
