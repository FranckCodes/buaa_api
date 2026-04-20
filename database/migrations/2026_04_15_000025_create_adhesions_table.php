<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('adhesions', function (Blueprint $table) {
            $table->string('id', 50)->primary();

            $table->string('client_id', 50);
            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();

            $table->string('union_id', 50);
            $table->foreign('union_id')->references('id')->on('unions')->cascadeOnDelete();

            $table->foreignId('adhesion_type_id')->constrained('adhesion_types');
            $table->foreignId('adhesion_status_id')->constrained('adhesion_statuses');

            $table->string('numero_membre')->unique();
            $table->date('date_adhesion');
            $table->date('prochaine_echeance')->nullable();

            $table->decimal('cotisation_initiale', 12, 2)->default(0);
            $table->decimal('cotisation_annuelle', 12, 2)->default(0);

            $table->foreignId('payment_mode_id')->nullable()->constrained('payment_modes')->nullOnDelete();
            $table->json('avantages')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adhesions');
    }
};
