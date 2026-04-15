<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('credit_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_id')->constrained('credits')->cascadeOnDelete();
            $table->integer('periode_annee');
            $table->integer('periode_mois');
            $table->decimal('montant', 12, 2);
            $table->string('statut')->default('en_attente');
            $table->date('date_paiement')->nullable();
            $table->date('date_echeance');
            $table->timestamps();

            $table->unique(['credit_id', 'periode_annee', 'periode_mois']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_payments');
    }
};
