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
    Schema::create('tariffs', function (Blueprint $table) {
        $table->id();
        // Fait le lien avec la chambre (ex: Chambre Double)
        $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
        // Le prix (jusqu'à 999 999.99)
        $table->decimal('price', 8, 2);
        // Les dates de validité du tarif (ex: plus cher en été)
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariffs');
    }
};
