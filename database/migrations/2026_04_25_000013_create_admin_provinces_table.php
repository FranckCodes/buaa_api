<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_provinces', function (Blueprint $table) {
            $table->id();

            $table->string('admin_id', 50);
            $table->foreign('admin_id')->references('id')->on('admins')->cascadeOnDelete();

            $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Un admin ne peut pas avoir deux fois la même province
            $table->unique(['admin_id', 'province_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_provinces');
    }
};
