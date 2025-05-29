<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('bon_commandes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->date('date_commande');
            $table->unsignedBigInteger('fournisseur_id');
            $table->unsignedBigInteger('demande_approvisionnement_id')->nullable();
            $table->unsignedBigInteger('demande_achat_id')->nullable();
            $table->unsignedBigInteger('user_id'); // Créé par
            $table->decimal('montant_total', 15, 2)->default(0);
            $table->date('date_livraison_prevue')->nullable();
            $table->enum('statut', ['en attente', 'confirmée', 'livrée', 'annulée'])->default('en attente');
            $table->text('conditions_paiement')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('fournisseur_id')->references('id')->on('client_fournisseurs')->onDelete('cascade');
            $table->foreign('demande_approvisionnement_id')->references('id')->on('demande_approvisionnements')->onDelete('set null');
            $table->foreign('demande_achat_id')->references('id')->on('demande_achats')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('lignes_bon_commande', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bon_commande_id');
            $table->unsignedBigInteger('article_id');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 15, 2);
            $table->decimal('montant', 15, 2)->virtualAs('quantite * prix_unitaire');
            $table->integer('quantite_livree')->default(0);
            $table->text('commentaire')->nullable();
            $table->timestamps();
            
            $table->foreign('bon_commande_id')->references('id')->on('bon_commandes')->onDelete('cascade');
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('lignes_bon_commande');
        Schema::dropIfExists('bon_commandes');
    }
};