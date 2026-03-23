<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_corps_de_metier');
            $table->text('description');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->integer('nbre_jr_previsionnelle')->default(0);
            $table->integer('nbre_de_jr_realise')->default(0);
            $table->decimal('progression', 5, 2)->default(0);
            $table->enum('statut', ['non_debute', 'en_cours', 'suspendu', 'receptionne', 'termine'])->default('non_debute');
            $table->unsignedBigInteger('id_contrat');
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('id_corps_de_metier')->references('id')->on('corps_de_metiers')->onDelete('cascade');
            $table->foreign('id_contrat')->references('id')->on('contrats')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('taches');
    }
};
