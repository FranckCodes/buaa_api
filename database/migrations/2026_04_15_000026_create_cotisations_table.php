<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cotisations', function (Blueprint $table) {
            $table->id();
            $table->string('adhesion_id');
            $table->foreign('adhesion_id')->references('id')->on('adhesions')->cascadeOnDelete();

            $table->integer('annee');
            $table->decimal('montant', 12, 2);
            $table->string('statut')->default('en_attente');
            $table->date('date_paiement')->nullable();
            $table->foreignId('payment_mode_id')->nullable()->constrained('payment_modes')->nullOnDelete();
            $table->string('reference_recu')->nullable();
            $table->timestamps();

            $table->unique(['adhesion_id', 'annee']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotisations');
    }
};
