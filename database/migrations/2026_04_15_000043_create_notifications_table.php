<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->string('user_id', 50);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->string('category');
            $table->string('type');
            $table->string('title');
            $table->text('body')->nullable();

            $table->boolean('is_read')->default(false);

            $table->string('action_label')->nullable();
            $table->string('action_url')->nullable();

            $table->string('from_user_id', 50)->nullable();
            $table->foreign('from_user_id')->references('id')->on('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
