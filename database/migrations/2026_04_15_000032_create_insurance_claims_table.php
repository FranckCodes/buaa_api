<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurance_id')->constrained('insurances')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('type_sinistre');
            $table->decimal('montant_reclame', 12, 2);
            $table->decimal('montant_approuve', 12, 2)->nullable();
            $table->string('statut')->default('en_analyse');
            $table->text('description')->nullable();
            $table->date('date_sinistre')->nullable();
            $table->date('date_soumission');
            $table->foreignId('traite_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
    }
};
