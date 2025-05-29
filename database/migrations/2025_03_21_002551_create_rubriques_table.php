<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRubriquesTable extends Migration
{
    public function up()
    {
        Schema::create('rubriques', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Nom de la rubrique
            $table->unsignedBigInteger('id_soussession'); // Clé étrangère vers souscategorierubriques
            $table->string('type')->default('bpu'); // Type de la rubrique
            $table->timestamps();

            // Définition de la clé étrangère
            $table->foreign('id_soussession')->references('id')->on('souscategorierubriques')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rubriques');
    }
}