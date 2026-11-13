<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // On ajoute la colonne room_id et on crée la relation (clé étrangère) avec la table rooms
            // On la met nullable() au cas où tu aurais de vieilles données de test dans la table
            $table->foreignId('room_id')->nullable()->after('id')->constrained('rooms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
        });
    }
};
