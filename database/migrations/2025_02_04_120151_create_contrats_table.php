<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->string('ref_contrat')->unique();
            $table->string('nom_contrat');
            $table->unsignedBigInteger('id_projet');
            $table->string('nom_projet');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->string('type_travaux');
            $table->decimal('taux_garantie', 5, 2);
            $table->foreignId('client_id')->constrained('client_fournisseurs')->onDelete('cascade');
            $table->decimal('montant', 15, 2);
            $table->enum('statut', ['en cours', 'terminé', 'annulé'])->default('en cours');
            $table->boolean('decompte')->default(false);
            $table->timestamps();
            $table->foreign('id_projet')->references('id')->on('projets')->onDelete('cascade');

        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
