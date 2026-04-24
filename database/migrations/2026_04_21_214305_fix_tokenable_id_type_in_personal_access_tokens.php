<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the composite index before altering the column
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropIndex('personal_access_tokens_tokenable_type_tokenable_id_index');
        });

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->string('tokenable_id', 50)->change();
            $table->index(['tokenable_type', 'tokenable_id']);
        });
    }

    public function down(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropIndex('personal_access_tokens_tokenable_type_tokenable_id_index');
        });

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('tokenable_id')->change();
            $table->index(['tokenable_type', 'tokenable_id']);
        });
    }
};
