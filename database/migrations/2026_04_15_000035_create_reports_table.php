<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('superviseur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('report_type_id')->constrained('report_types');
            $table->foreignId('report_status_id')->constrained('report_statuses');
            $table->string('summary', 300)->nullable();
            $table->decimal('value_numeric', 12, 2)->nullable();
            $table->string('value_unit')->nullable();
            $table->string('value_text')->nullable();
            $table->text('details')->nullable();
            $table->date('date_rapport');
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->text('motif_rejet')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
