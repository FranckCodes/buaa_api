<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('insurance_beneficiaries', function (Blueprint $table) {
            $table->id();

            $table->string('insurance_id', 50);
            $table->foreign('insurance_id')->references('id')->on('insurances')->cascadeOnDelete();

            $table->string('nom');
            $table->unsignedInteger('age')->nullable();
            $table->string('relation')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_beneficiaries');
    }
};
