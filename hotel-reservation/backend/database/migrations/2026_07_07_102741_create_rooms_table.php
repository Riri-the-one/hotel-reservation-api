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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            
            // Voici la Clé Étrangère (FK) qui relie cette chambre à un type dans la table room_types
            $table->foreignId('room_type_id')->constrained('room_types')->onDelete('cascade');
            
            $table->string('room_number')->unique(); // Le numéro de la chambre, 'unique' empêche les doublons
            $table->enum('status', ['disponible', 'maintenance'])->default('disponible'); // Le statut par défaut
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};