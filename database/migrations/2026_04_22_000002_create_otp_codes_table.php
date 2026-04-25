<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();

            $table->string('user_id', 50)->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            $table->string('telephone', 50);
            $table->string('code', 10);
            $table->string('purpose', 50)->default('login');
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->boolean('is_used')->default(false);
            $table->timestamps();

            $table->index(['telephone', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
