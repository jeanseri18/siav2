<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('localisations', function (Blueprint $table) {
            $table->id();
            $table->string('titre_localisation');
            $table->unsignedBigInteger('id_niveau');
            $table->unsignedBigInteger('id_contrat');
            $table->timestamps();

            $table->foreign('id_niveau')->references('id')->on('niveaux')->onDelete('cascade');
            $table->foreign('id_contrat')->references('id')->on('contrats')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('localisations');
    }
};
