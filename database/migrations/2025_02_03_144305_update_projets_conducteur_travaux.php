<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('projets', function (Blueprint $table) {
            // Supprimer l'ancien champ conducteur_travaux (string)
            $table->dropColumn('conducteur_travaux');
            
            // Ajouter les nouvelles clés étrangères
            $table->unsignedBigInteger('conducteur_travaux_id')->after('secteur_activite_id')->nullable();
            $table->unsignedBigInteger('chef_projet_id')->after('conducteur_travaux_id')->nullable();
            
            // Créer les contraintes de clés étrangères
            $table->foreign('conducteur_travaux_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('chef_projet_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down() {
        Schema::table('projets', function (Blueprint $table) {
            // Supprimer les contraintes de clés étrangères
            $table->dropForeign(['conducteur_travaux_id']);
            $table->dropForeign(['chef_projet_id']);
            
            // Supprimer les colonnes
            $table->dropColumn(['conducteur_travaux_id', 'chef_projet_id']);
            
            // Remettre l'ancien champ
            $table->string('conducteur_travaux')->after('secteur_activite_id');
        });
    }
};