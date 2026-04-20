<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();

            $table->string('sender_id', 50);
            $table->foreign('sender_id')->references('id')->on('users')->cascadeOnDelete();

            $table->longText('text')->nullable();
            $table->string('type')->default('text');
            $table->string('image_url')->nullable();
            $table->string('file_url')->nullable();

            $table->foreignId('reply_to_message_id')->nullable()->constrained('messages')->nullOnDelete();

            $table->string('status')->default('sent');
            $table->timestamp('deleted_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
