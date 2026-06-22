<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * WHY THIS MIGRATION EXISTS:
     * booking_slots was previously keyed only by (date, time, workstation).
     * That means two different lifts (e.g. four-post and two-post) booked
     * at the same date/time/workstation collided into the SAME row, and
     * whichever booking was created/updated last silently stole the slot
     * from the other lift. This caused: wrong calendar colors, missing
     * tooltips, and dates that should be fully-booked-for-that-lift not
     * disabling.
     *
     * Adding lift_type here lets each lift track its own slot rows
     * independently, matching how `bookings.lift_type` already works.
     */
    public function up(): void
    {
        Schema::table('booking_slots', function (Blueprint $table) {
            $table->string('lift_type')->nullable()->after('booking_id');
        });

        // Backfill lift_type on existing rows from the related booking,
        // so historical data isn't orphaned/ambiguous after this change.
        DB::statement("
            UPDATE booking_slots bs
            INNER JOIN bookings b ON b.id = bs.booking_id
            SET bs.lift_type = b.lift_type
            WHERE bs.lift_type IS NULL
        ");

        // Now that data is backfilled, make it required and add it to the
        // composite uniqueness so each lift gets its own slot row.
        Schema::table('booking_slots', function (Blueprint $table) {
            $table->string('lift_type')->nullable(false)->change();
            $table->unique(['date', 'time', 'workstation', 'lift_type'], 'booking_slots_date_time_ws_lift_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_slots', function (Blueprint $table) {
            $table->dropUnique('booking_slots_date_time_ws_lift_unique');
            $table->dropColumn('lift_type');
        });
    }
};