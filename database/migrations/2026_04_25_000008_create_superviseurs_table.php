<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('superviseurs', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->foreign('id')->references('id')->on('users')->cascadeOnDelete();

            $table->string('matricule', 50)->unique()->nullable();
            $table->string('telephone_pro', 20)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('superviseurs');
    }
};
