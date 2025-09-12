<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('retour_approvisionnement', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bon_commande_id');
            $table->unsignedBigInteger('article_id');
            $table->unsignedBigInteger('projet_id');
            $table->integer('quantite_retournee');
            $table->date('date_retour');
            $table->text('motif')->nullable();
            $table->enum('statut', ['en_attente', 'accepté', 'refusé'])->default('en_attente');
            $table->timestamps();
            
            $table->foreign('bon_commande_id')->references('id')->on('bon_commandes')->onDelete('cascade');
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
            $table->foreign('projet_id')->references('id')->on('projets')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('retour_approvisionnement');
    }
};