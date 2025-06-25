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
        Schema::create('ligne_receptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reception_id')->constrained('receptions')->onDelete('cascade');
            $table->foreignId('ligne_bon_commande_id')->constrained('ligne_bon_commandes')->onDelete('cascade');
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->decimal('quantite_recue', 10, 2);
            $table->decimal('quantite_conforme', 10, 2)->default(0);
            $table->decimal('quantite_non_conforme', 10, 2)->default(0);
            $table->decimal('prix_unitaire_recu', 10, 2);
            $table->text('observations')->nullable();
            $table->string('numero_lot')->nullable();
            $table->date('date_peremption')->nullable();
            $table->enum('etat_article', ['neuf', 'bon', 'acceptable', 'defectueux', 'endommage'])->default('neuf');
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour améliorer les performances
            $table->index(['reception_id', 'article_id']);
            $table->index(['ligne_bon_commande_id']);
            $table->index(['etat_article']);
            $table->index(['date_peremption']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_receptions');
    }
};