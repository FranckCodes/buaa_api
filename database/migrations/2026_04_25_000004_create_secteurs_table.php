<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('secteurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('territoire_id')->constrained('territoires')->cascadeOnDelete();
            $table->string('designation', 191);
            $table->timestamps();

            $table->unique(['territoire_id', 'designation']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secteurs');
    }
};
