<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ville_id')->constrained('villes')->cascadeOnDelete();
            $table->string('designation', 191);
            $table->timestamps();

            $table->unique(['ville_id', 'designation']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communes');
    }
};
