<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('insurance_type_id')->constrained('insurance_types');
            $table->foreignId('insurance_status_id')->constrained('insurance_statuses');

            $table->decimal('montant_annuel', 12, 2);
            $table->date('date_souscription');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->date('prochaine_echeance')->nullable();
            $table->text('description')->nullable();
            $table->json('couvertures')->nullable();

            $table->string('etablissement')->nullable();
            $table->string('niveau_etude')->nullable();

            $table->decimal('superficie_hectares', 12, 2)->nullable();
            $table->string('type_culture')->nullable();
            $table->decimal('valeur_materiel', 12, 2)->nullable();

            $table->text('antecedents_medicaux')->nullable();
            $table->string('medecin_traitant')->nullable();

            $table->foreignId('traite_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurances');
    }
};
