<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('document', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('chemin');
            $table->unsignedBigInteger('id_projet')->nullable();
            $table->unsignedBigInteger('id_contrat')->nullable();
            $table->unsignedBigInteger('id_facture')->nullable();
            $table->timestamps();

         
        });
    }

    public function down() {
        Schema::dropIfExists('document');
    }
};
