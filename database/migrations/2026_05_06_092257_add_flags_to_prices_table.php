<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->boolean('is_membership')->default(0);
            $table->boolean('is_active')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
     public function down(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn(['is_membership', 'is_active']);
        });
    }
};
