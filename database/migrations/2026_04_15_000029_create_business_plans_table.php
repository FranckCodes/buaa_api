<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('business_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_id')->unique()->constrained('credits')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('titre');
            $table->text('resume')->nullable();
            $table->longText('description')->nullable();
            $table->text('retour_investissement')->nullable();
            $table->string('statut')->default('en_analyse');
            $table->unsignedTinyInteger('score')->nullable();
            $table->date('date_soumission');
            $table->foreignId('evalue_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_plans');
    }
};
