<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('demande_cotations', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->date('date_demande');
            $table->unsignedBigInteger('demande_achat_id')->nullable();
            $table->date('date_expiration');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->enum('statut', ['en cours', 'terminée', 'annulée'])->default('en cours');
            $table->text('conditions_generales')->nullable();
            $table->timestamps();
            
            $table->foreign('demande_achat_id')->references('id')->on('demande_achats')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('fournisseurs_demande_cotation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demande_cotation_id');
            $table->unsignedBigInteger('fournisseur_id');
            $table->boolean('repondu')->default(false);
            $table->date('date_reponse')->nullable();
            $table->decimal('montant_total', 15, 2)->nullable();
            $table->boolean('retenu')->default(false);
            $table->text('commentaire')->nullable();
            $table->timestamps();
            
            $table->foreign('demande_cotation_id')->references('id')->on('demande_cotations')->onDelete('cascade');
            $table->foreign('fournisseur_id')->references('id')->on('client_fournisseurs')->onDelete('cascade');
        });

        Schema::create('lignes_demande_cotation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demande_cotation_id');
            $table->unsignedBigInteger('article_id')->nullable();
            $table->string('designation')->nullable(); // Pour les articles non existants
            $table->integer('quantite');
            $table->string('unite_mesure')->nullable();
            $table->text('specifications')->nullable();
            $table->timestamps();
            
            $table->foreign('demande_cotation_id')->references('id')->on('demande_cotations')->onDelete('cascade');
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('set null');
        });
    }

    public function down() {
        Schema::dropIfExists('lignes_demande_cotation');
        Schema::dropIfExists('fournisseurs_demande_cotation');
        Schema::dropIfExists('demande_cotations');
    }
};