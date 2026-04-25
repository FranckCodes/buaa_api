<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('territoires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete();
            $table->string('designation', 191);
            $table->timestamps();

            $table->unique(['province_id', 'designation']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('territoires');
    }
};
