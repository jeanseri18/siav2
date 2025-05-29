<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpusTable extends Migration
{
    public function up()
    {
        Schema::create('bpus', function (Blueprint $table) {
            $table->id();
            $table->string('designation'); // Nom du BPU
            $table->decimal('qte', 10, 2)->default(0); // Quantité
            $table->decimal('materiaux', 10, 2)->default(0); // Coût des matériaux
            $table->string('unite'); // Unité de mesure
            $table->decimal('main_oeuvre', 10, 2)->default(0); // Coût de la main d'œuvre
            $table->decimal('materiel', 10, 2)->default(0); // Coût du matériel
            $table->decimal('debourse_sec', 10, 2)->nullable(); // Déboursé sec (nullable)
            $table->decimal('frais_chantier', 10, 2)->default(0); // Frais de chantier
            $table->decimal('frais_general', 10, 2)->default(0); // Frais généraux
            $table->decimal('marge_nette', 10, 2)->default(0); // Marge nette
            $table->decimal('pu_ht', 10, 2)->default(0); // Prix unitaire hors taxe
            $table->decimal('pu_ttc', 10, 2)->default(0); // Prix unitaire toutes taxes comprises
            $table->unsignedBigInteger('id_rubrique'); // Clé étrangère vers rubriques
            $table->timestamps();

            // Définition de la clé étrangère
            $table->foreign('id_rubrique')->references('id')->on('rubriques')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bpus');
    }
}