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
        Schema::create('receptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bon_commande_id')->constrained('bon_commandes')->onDelete('cascade');
            $table->string('numero_reception')->unique();
            $table->datetime('date_reception');
            $table->string('numero_bon_livraison')->nullable();
            $table->string('transporteur')->nullable();
            $table->text('observations')->nullable();
            $table->enum('statut', ['en_cours', 'complete', 'partielle', 'annulee'])->default('en_cours');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('quantite_totale_recue', 10, 2)->default(0);
            $table->decimal('montant_total_recu', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour améliorer les performances
            $table->index(['bon_commande_id', 'statut']);
            $table->index(['date_reception']);
            $table->index(['numero_reception']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receptions');
    }
};