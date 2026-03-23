<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->unsignedBigInteger('id_contrat');
            $table->timestamps();

            $table->foreign('id_contrat')->references('id')->on('contrats')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lots');
    }
};
