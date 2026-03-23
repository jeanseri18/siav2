<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('corps_de_metiers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_localisation');
            $table->string('nom_corpsdemetier');
            $table->unsignedBigInteger('id_contrat');
            $table->timestamps();

            $table->foreign('id_localisation')->references('id')->on('localisations')->onDelete('cascade');
            $table->foreign('id_contrat')->references('id')->on('contrats')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('corps_de_metiers');
    }
};
