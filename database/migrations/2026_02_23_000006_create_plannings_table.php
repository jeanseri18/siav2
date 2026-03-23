<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plannings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_contrat');
            $table->unsignedBigInteger('id_souscategorie')->nullable();
            $table->string('nom_tache_planning');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->enum('statut', ['non_demarre', 'en_cours', 'retard', 'termine'])->default('non_demarre');
            $table->timestamps();

            $table->foreign('id_contrat')->references('id')->on('contrats')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('plannings');
    }
};
