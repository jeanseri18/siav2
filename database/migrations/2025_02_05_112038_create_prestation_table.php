<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('prestation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_artisan')->nullable();
            $table->unsignedBigInteger('id_contrat');
            $table->string('prestation_titre')->nullable();
            $table->string('detail')->nullable();
            $table->enum('statut', ['en attente', 'en cours', 'terminée', 'annulée'])->default('en cours');
            $table->timestamps();

            $table->foreign('id_artisan')->references('id')->on('artisan')->onDelete('set null');
            $table->foreign('id_contrat')->references('id')->on('contrats')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('prestation');
    }
};
