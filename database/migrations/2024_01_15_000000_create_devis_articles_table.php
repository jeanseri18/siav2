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
        Schema::create('devis_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devis_id')->constrained('devis')->onDelete('cascade');
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->integer('quantite');
            $table->decimal('prix_unitaire_ht', 15, 2);
            $table->decimal('montant_total', 15, 2);
            $table->timestamps();
            
            $table->unique(['devis_id', 'article_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devis_articles');
    }
};