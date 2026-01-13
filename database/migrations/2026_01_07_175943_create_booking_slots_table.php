<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking_slots', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();

            $table->date('date');
            $table->time('time'); // 09:00, 10:00 etc
            $table->unsignedTinyInteger('workstation')->default(1);

            $table->enum('status', ['booked', 'blocked'])->default('booked');

            $table->timestamps();

            //  Prevent double booking
            $table->unique(['date', 'time', 'workstation']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_slots');
    }
};
