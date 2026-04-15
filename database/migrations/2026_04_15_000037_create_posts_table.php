<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('author_id');
            $table->foreign('author_id')->references('id')->on('users')->cascadeOnDelete();
            $table->longText('content');
            $table->foreignId('post_tag_id')->constrained('post_tags');
            $table->foreignId('post_status_id')->constrained('post_statuses');
            $table->string('valide_par')->nullable();
            $table->foreign('valide_par')->references('id')->on('users')->nullOnDelete();
            $table->text('motif_rejet')->nullable();
            $table->unsignedInteger('likes_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
