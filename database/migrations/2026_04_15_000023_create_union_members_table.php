<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('union_members', function (Blueprint $table) {
            $table->id();
            $table->string('union_id');
            $table->foreign('union_id')->references('id')->on('unions')->cascadeOnDelete();

            $table->string('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            $table->string('nom_complet')->nullable();
            $table->string('telephone')->nullable();
            $table->string('role_dans_union');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('union_members');
    }
};
