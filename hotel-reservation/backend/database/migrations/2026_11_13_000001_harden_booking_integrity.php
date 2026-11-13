<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
        });
        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedSmallInteger('guests')->default(1);
            $table->uuid('request_key')->nullable()->unique();
            $table->index(['room_id', 'status', 'check_in', 'check_out'], 'reservation_availability');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex('reservation_availability');
            $table->dropUnique(['request_key']);
            $table->dropColumn(['guests', 'request_key']);
        });
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_admin'));
    }
};
