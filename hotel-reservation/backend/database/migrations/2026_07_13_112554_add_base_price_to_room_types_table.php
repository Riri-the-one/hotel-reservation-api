<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            // On ajoute le prix de base, avec 0 par défaut pour éviter les erreurs sur les anciennes chambres
            $table->decimal('base_price', 8, 2)->default(0)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropColumn('base_price');
        });
    }
};