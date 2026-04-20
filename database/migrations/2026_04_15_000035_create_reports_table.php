<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->string('id', 50)->primary();

            $table->string('client_id', 50);
            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();

            $table->string('superviseur_id', 50)->nullable();
            $table->foreign('superviseur_id')->references('id')->on('users')->nullOnDelete();

            $table->foreignId('report_type_id')->constrained('report_types');
            $table->foreignId('report_status_id')->constrained('report_statuses');

            $table->string('summary', 300)->nullable();
            $table->decimal('value_numeric', 12, 2)->nullable();
            $table->string('value_unit')->nullable();
            $table->string('value_text')->nullable();
            $table->text('details')->nullable();

            $table->date('date_rapport');

            $table->string('valide_par')->nullable();
            $table->foreign('valide_par')->references('id')->on('users')->nullOnDelete();

            $table->text('motif_rejet')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
