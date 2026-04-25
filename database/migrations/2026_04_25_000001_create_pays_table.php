<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pays', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();       // alpha3 ex: COD, FRA
            $table->string('designation', 191);         // nom en français
            $table->string('picture', 191)->nullable(); // code alpha3 utilisé comme clé drapeau
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pays');
    }
};
