<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('membership_requests', function (Blueprint $table) {
            $table->timestamp('last_request_at')->nullable()->after('approved_at');
        });
    }

    public function down()
    {
        Schema::table('membership_requests', function (Blueprint $table) {
            $table->dropColumn('last_request_at');
        });
    }
};