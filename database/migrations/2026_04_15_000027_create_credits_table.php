<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('credit_type_id')->constrained('credit_types');
            $table->foreignId('credit_status_id')->constrained('credit_statuses');

            $table->decimal('montant_demande', 12, 2);
            $table->decimal('montant_approuve', 12, 2)->nullable();
            $table->decimal('montant_rembourse', 12, 2)->default(0);

            $table->date('date_demande');
            $table->date('date_approbation')->nullable();
            $table->integer('duree_mois');
            $table->decimal('taux_interet', 5, 2)->nullable();
            $table->date('prochaine_echeance')->nullable();
            $table->decimal('montant_echeance', 12, 2)->nullable();

            $table->text('objet_credit')->nullable();
            $table->text('description_projet')->nullable();
            $table->text('retour_investissement')->nullable();
            $table->decimal('revenus_mensuels', 12, 2)->nullable();
            $table->boolean('autres_credits')->default(false);
            $table->decimal('montant_autres_credits', 12, 2)->nullable();

            $table->foreignId('traite_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
