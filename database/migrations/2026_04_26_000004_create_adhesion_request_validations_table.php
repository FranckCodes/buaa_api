<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('adhesion_request_validations', function (Blueprint $table) {
            $table->id();

            $table->string('adhesion_request_id', 50);
            $table->foreign('adhesion_request_id')
                ->references('id')->on('adhesion_requests')
                ->cascadeOnDelete();

            // Niveau de validation : president | superviseur | admin
            $table->string('level', 20);

            // Décision : en_attente | valide | rejete
            $table->string('decision', 20)->default('en_attente');

            // Acteur de la décision
            $table->string('validator_id', 50)->nullable();
            $table->foreign('validator_id')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->text('motif')->nullable();
            $table->timestamp('decided_at')->nullable();

            $table->timestamps();

            // Une seule entrée par niveau pour une demande donnée
            $table->unique(['adhesion_request_id', 'level']);
            $table->index(['validator_id', 'decision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adhesion_request_validations');
    }
};
