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
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_projet_id');
            $table->enum('type_mouvement', ['entree', 'sortie', 'transfert', 'livraison_chantier', 'retour_chantier', 'retour_projet']);
            $table->decimal('quantite', 10, 2);
            $table->decimal('quantite_avant', 10, 2);
            $table->decimal('quantite_apres', 10, 2);
            $table->string('reference_mouvement')->nullable(); // Référence du document source
            $table->text('commentaires')->nullable();
            $table->date('date_mouvement');
            $table->unsignedBigInteger('user_id')->nullable(); // Utilisateur qui a effectué le mouvement
            $table->json('donnees_supplementaires')->nullable(); // Pour stocker des infos additionnelles
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['stock_projet_id', 'type_mouvement']);
            $table->index(['date_mouvement']);
            $table->index(['type_mouvement']);
            
            // Clés étrangères
            $table->foreign('stock_projet_id')->references('id')->on('stock_projet')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};