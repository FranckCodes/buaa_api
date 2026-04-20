<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->string('id', 50)->primary();

            $table->string('client_id', 50);
            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();

            $table->foreignId('order_type_id')->constrained('order_types');
            $table->foreignId('order_status_id')->constrained('order_statuses');

            $table->decimal('montant', 12, 2)->nullable();
            $table->text('description')->nullable();
            $table->text('justification')->nullable();
            $table->decimal('quantite', 12, 2)->nullable();
            $table->string('unite')->nullable();
            $table->string('priorite')->default('moyenne');
            $table->unsignedTinyInteger('progression')->default(0);

            $table->date('date_soumission');

            $table->string('traite_par')->nullable();
            $table->foreign('traite_par')->references('id')->on('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
