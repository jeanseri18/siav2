<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('niveaux', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_lot');
            $table->string('titre_niveau');
            $table->unsignedBigInteger('id_contrat');
            $table->timestamps();

            $table->foreign('id_lot')->references('id')->on('lots')->onDelete('cascade');
            $table->foreign('id_contrat')->references('id')->on('contrats')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('niveaux');
    }
};
