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
        Schema::create('decomptes', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->decimal('montant', 15, 2)->default(0);
            $table->decimal('pourcentage', 5, 2)->default(0); // Pourcentage du décompte
            $table->unsignedBigInteger('id_prestation')->nullable();
            $table->timestamps();
            
            // Clé étrangère
            $table->foreign('id_prestation')->references('id')->on('prestation')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decomptes');
    }
};
