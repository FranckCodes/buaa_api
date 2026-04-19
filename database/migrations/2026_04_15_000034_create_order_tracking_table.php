<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_tracking', function (Blueprint $table) {
            $table->id();

            $table->string('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();

            $table->string('label');
            $table->boolean('done')->default(false);
            $table->date('date_done')->nullable();
            $table->unsignedInteger('ordre');

            $table->timestamps();

            $table->unique(['order_id', 'ordre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_tracking');
    }
};
