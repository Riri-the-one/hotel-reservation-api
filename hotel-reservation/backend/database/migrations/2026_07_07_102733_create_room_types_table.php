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
        Schema::create('room_types', function (Blueprint $table) {
            $table->id(); // Ceci crée la fameuse PK (Clé Primaire)
            $table->string('name'); // ex: Simple, Double, Suite
            $table->text('description')->nullable(); // nullable veut dire que ce champ n'est pas obligatoire
            $table->integer('capacity'); // Nombre de personnes max
            $table->timestamps(); // Ceci crée automatiquement created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};