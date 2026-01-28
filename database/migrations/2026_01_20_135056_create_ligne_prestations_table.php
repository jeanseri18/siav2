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
        Schema::create('ligne_prestations', function (Blueprint $table) {
            $table->id();
            $table->string('designation');
            $table->string('unite')->nullable();
            $table->decimal('quantite', 10, 2)->default(0);
            $table->decimal('cout_unitaire', 15, 2)->default(0); // Coût unitaire (saisie manuelle)
            $table->decimal('taux_avancement', 5, 2)->default(0); // Taux d'avancement en %
            $table->decimal('montant', 15, 2)->default(0); // Montant total
            $table->decimal('montant_paye', 15, 2)->default(0); // Montant payé
            $table->decimal('montant_reste', 15, 2)->default(0); // Montant restant
            $table->unsignedBigInteger('id_rubrique')->nullable();
            $table->unsignedBigInteger('id_prestation');
            $table->timestamps();
            
            // Clés étrangères
            $table->foreign('id_rubrique')->references('id')->on('rubriques')->onDelete('set null');
            $table->foreign('id_prestation')->references('id')->on('prestation')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_prestations');
    }
};
