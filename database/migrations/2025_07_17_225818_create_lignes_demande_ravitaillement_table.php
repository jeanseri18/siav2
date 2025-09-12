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
        Schema::create('lignes_demande_ravitaillement', function (Blueprint $table) {
            $table->id();
            $table->decimal('quantite_demandee', 15, 3);
            $table->decimal('quantite_approuvee', 15, 3)->nullable();
            $table->decimal('quantite_livree', 15, 3)->nullable();
            $table->decimal('prix_unitaire_estime', 15, 2)->nullable();
            $table->decimal('prix_unitaire_reel', 15, 2)->nullable();
            $table->decimal('montant_estime', 15, 2)->nullable();
            $table->decimal('montant_reel', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->text('commentaires')->nullable();
            
            // Relations
            $table->foreignId('demande_ravitaillement_id')->constrained('demandes_ravitaillement')->onDelete('cascade');
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->foreignId('unite_mesure_id')->nullable()->constrained('unite_mesures')->onDelete('set null');
            
            $table->timestamps();
            
            // Index
            $table->index(['demande_ravitaillement_id', 'article_id'], 'idx_ligne_demande_ravit_demande_article');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lignes_demande_ravitaillement');
    }
};
