<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('business_plans', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->string('credit_id')->unique();
            $table->foreign('credit_id')->references('id')->on('credits')->cascadeOnDelete();

            $table->string('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();

            $table->string('titre');
            $table->text('resume')->nullable();
            $table->longText('description')->nullable();
            $table->text('retour_investissement')->nullable();
            $table->string('statut')->default('en_analyse');
            $table->unsignedTinyInteger('score')->nullable();
            $table->date('date_soumission');
            $table->string('evalue_par')->nullable();
            $table->foreign('evalue_par')->references('id')->on('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_plans');
    }
};
