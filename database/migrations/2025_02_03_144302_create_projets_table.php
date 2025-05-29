<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('projets', function (Blueprint $table) {
            $table->id();
            $table->string('ref_projet')->unique();
            $table->date('date_creation');
            $table->string('nom_projet');
            $table->text('description')->nullable();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->string('client');
            $table->foreignId('secteur_activite_id')->constrained('secteur_activites')->onDelete('cascade');
            $table->string('conducteur_travaux');
            $table->boolean('hastva')->default(false);
            $table->enum('statut', ['en cours', 'terminé', 'annulé'])->default('en cours');
            $table->foreignId('bu_id')->constrained('bus')->onDelete('cascade');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};
