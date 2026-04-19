<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('adhesions', function (Blueprint $table) {
            $table->id();
            $table->string('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();
            $table->foreignId('union_id')->constrained('unions')->cascadeOnDelete();
            $table->foreignId('adhesion_type_id')->constrained('adhesion_types');
            $table->foreignId('adhesion_status_id')->constrained('adhesion_statuses');
            $table->string('numero_membre')->nullable()->unique();
            $table->date('date_adhesion');
            $table->date('prochaine_echeance')->nullable();
            $table->decimal('cotisation_initiale', 12, 2);
            $table->decimal('cotisation_annuelle', 12, 2);
            $table->foreignId('payment_mode_id')->nullable()->constrained('payment_modes')->nullOnDelete();
            $table->json('avantages')->nullable();
            $table->timestamps();

            $table->unique(['client_id', 'union_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adhesions');
    }
};
