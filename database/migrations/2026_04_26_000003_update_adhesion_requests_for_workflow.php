<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Drop legacy string geo
        Schema::table('adhesion_requests', function (Blueprint $table) {
            $table->dropColumn('province');
        });

        // 2) Add union link + normalized geo + per-level summary fields
        Schema::table('adhesion_requests', function (Blueprint $table) {
            // Union ciblée par la demande (le client demande à rejoindre une union précise)
            $table->string('union_id', 50)->nullable()->after('demandeur_type');
            $table->foreign('union_id')
                ->references('id')->on('unions')
                ->nullOnDelete();

            // Demandeur (client) — lien direct vers le user qui soumet
            $table->string('client_id', 50)->nullable()->after('union_id');
            $table->foreign('client_id')
                ->references('id')->on('clients')
                ->nullOnDelete();

            // Géo normalisée
            $table->foreignId('province_id')->nullable()->after('adresse')->constrained('provinces')->nullOnDelete();
            $table->foreignId('territoire_id')->nullable()->after('province_id')->constrained('territoires')->nullOnDelete();
            $table->foreignId('secteur_id')->nullable()->after('territoire_id')->constrained('secteurs')->nullOnDelete();
            $table->foreignId('ville_id')->nullable()->after('secteur_id')->constrained('villes')->nullOnDelete();
            $table->foreignId('commune_id')->nullable()->after('ville_id')->constrained('communes')->nullOnDelete();

            // Synthèse du workflow 3 niveaux (le détail est dans adhesion_request_validations)
            // Valeurs possibles : en_attente, valide_president, valide_superviseur, accepte, rejete
            $table->string('etape_courante')->default('en_attente')->after('statut');

            // Motif de rejet final (si statut = rejete)
            $table->text('motif_rejet')->nullable()->after('etape_courante');

            // Numéro d'adhésion généré à l'acceptation finale (lien vers adhesions.numero_membre)
            $table->string('numero_membre_attribue')->nullable()->after('motif_rejet');
        });
    }

    public function down(): void
    {
        Schema::table('adhesion_requests', function (Blueprint $table) {
            $table->dropForeign(['union_id']);
            $table->dropForeign(['client_id']);
            $table->dropForeign(['province_id']);
            $table->dropForeign(['territoire_id']);
            $table->dropForeign(['secteur_id']);
            $table->dropForeign(['ville_id']);
            $table->dropForeign(['commune_id']);

            $table->dropColumn([
                'union_id',
                'client_id',
                'province_id',
                'territoire_id',
                'secteur_id',
                'ville_id',
                'commune_id',
                'etape_courante',
                'motif_rejet',
                'numero_membre_attribue',
            ]);
        });

        Schema::table('adhesion_requests', function (Blueprint $table) {
            $table->string('province')->nullable();
        });
    }
};
