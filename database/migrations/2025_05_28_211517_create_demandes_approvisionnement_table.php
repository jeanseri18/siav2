<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('demande_approvisionnements', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->date('date_demande');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('projet_id')->nullable();
            $table->unsignedBigInteger('user_id'); // Demandeur
            $table->enum('statut', ['en attente', 'approuvée', 'rejetée', 'terminée'])->default('en attente');
            $table->string('motif_rejet')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();
            
            $table->foreign('projet_id')->references('id')->on('projets')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

Schema::create('lignes_demande_approvisionnement', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('demande_approvisionnement_id');
    $table->unsignedBigInteger('article_id');
    $table->integer('quantite_demandee');
    $table->integer('quantite_approuvee')->nullable();
    $table->text('commentaire')->nullable();
    $table->timestamps();

    $table->foreign('demande_approvisionnement_id')
        ->references('id')->on('demande_approvisionnements')
        ->onDelete('cascade')
        ->name('fk_dappr_id');

    $table->foreign('article_id')
        ->references('id')->on('articles')
        ->onDelete('cascade');
});

    }

    public function down() {
        Schema::dropIfExists('lignes_demande_approvisionnement');
        Schema::dropIfExists('demande_approvisionnements');
    }
};