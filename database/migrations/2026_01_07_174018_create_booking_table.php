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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

        $table->date('date');
        $table->time('start_time');
        $table->unsignedSmallInteger('hours');

        $table->string('lift_type');      // four, two, scissor...
        $table->unsignedTinyInteger('workstation')->default(1);

        $table->unsignedSmallInteger('package_hours'); // 1,9,18
        $table->decimal('rate_per_hour', 8, 2);
        $table->decimal('total', 10, 2);

        $table->enum('status', ['pending','confirmed','cancelled'])
              ->default('confirmed');

        $table->timestamps();

        
        $table->index(['date', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
