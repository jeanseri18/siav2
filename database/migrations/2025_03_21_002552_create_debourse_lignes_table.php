<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebourseLignesTable extends Migration
{
    public function up()
    {
        Schema::create('debourse_lignes', function (Blueprint $table) {
            $table->id();
            $table->string('libelle'); // Nom du déboursé
            $table->string('unite'); // Unité de mesure
            $table->decimal('qte', 10, 2); // Quantité
            $table->decimal('pu', 10, 2); // Prix unitaire
            $table->unsignedBigInteger('id_rubrique'); // Clé étrangère vers rubriques
            $table->timestamps();

            // Définition de la clé étrangère
            $table->foreign('id_rubrique')->references('id')->on('rubriques')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('debourse_lignes');
    }
}
