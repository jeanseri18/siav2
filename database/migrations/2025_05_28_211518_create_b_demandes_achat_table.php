<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('demande_achats', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->date('date_demande');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('projet_id')->nullable();
            $table->unsignedBigInteger('user_id'); // Demandeur
            $table->enum('priorite', ['basse', 'normale', 'haute', 'urgente'])->default('normale');
            $table->enum('statut', ['en attente', 'approuvée', 'rejetée', 'traitée'])->default('en attente');
            $table->string('motif_rejet')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->date('date_besoin')->nullable();
            $table->timestamps();
            
            $table->foreign('projet_id')->references('id')->on('projets')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('lignes_demande_achat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demande_achat_id');
            $table->unsignedBigInteger('article_id')->nullable();
            $table->string('designation')->nullable(); // Pour les articles non existants
            $table->integer('quantite');
            $table->string('unite_mesure')->nullable();
            $table->decimal('prix_estime', 15, 2)->nullable();
            $table->text('specifications')->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();
            
            $table->foreign('demande_achat_id')->references('id')->on('demande_achats')->onDelete('cascade');
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('set null');
        });
    }

    public function down() {
        Schema::dropIfExists('lignes_demande_achat');
        Schema::dropIfExists('demande_achats');
    }
};