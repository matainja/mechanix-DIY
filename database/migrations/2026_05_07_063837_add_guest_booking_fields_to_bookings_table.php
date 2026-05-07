<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('guest_name')->nullable()->after('user_id');
            $table->string('guest_phone')->nullable()->after('guest_name');
            $table->enum('booking_type', ['regular', 'guest'])->default('regular')->after('status');
            $table->timestamp('expires_at')->nullable()->after('booking_type');
        });

        Schema::table('booking_slots', function (Blueprint $table) {
            $table->enum('status', ['booked', 'pending'])->default('booked')->change();
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['guest_name', 'guest_phone', 'booking_type', 'expires_at']);
        });
    }
};