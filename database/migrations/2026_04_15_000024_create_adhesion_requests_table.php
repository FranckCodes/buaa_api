<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('adhesion_requests', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('demandeur_type');
            $table->foreignId('client_activity_type_id')->nullable()->constrained('client_activity_types')->nullOnDelete();
            $table->foreignId('client_structure_type_id')->nullable()->constrained('client_structure_types')->nullOnDelete();
            $table->string('representant')->nullable();
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->text('adresse')->nullable();
            $table->string('province')->nullable();
            $table->date('date_demande');
            $table->decimal('cotisation', 12, 2)->nullable();
            $table->string('statut')->default('en_attente');
            $table->integer('membres_nombre')->nullable();
            $table->decimal('superficie_totale', 12, 2)->nullable();
            $table->string('type_culture')->nullable();
            $table->integer('experience_annees')->nullable();
            $table->integer('nombre_animaux')->nullable();
            $table->string('type_elevage')->nullable();
            $table->string('traite_par')->nullable();
            $table->foreign('traite_par')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adhesion_requests');
    }
};
