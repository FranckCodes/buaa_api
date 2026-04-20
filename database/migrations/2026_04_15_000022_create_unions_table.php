<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('unions', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nom');
            $table->string('type');
            $table->string('province')->nullable();
            $table->string('ville')->nullable();
            $table->text('adresse')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->date('date_creation')->nullable();

            $table->string('president')->nullable();
            $table->string('secretaire')->nullable();
            $table->string('tresorier')->nullable();
            $table->string('commissaire')->nullable();

            $table->unsignedInteger('membres_total')->default(0);
            $table->decimal('superficie_totale', 12, 2)->nullable();
            $table->json('cultures_principales')->nullable();
            $table->json('services')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unions');
    }
};
