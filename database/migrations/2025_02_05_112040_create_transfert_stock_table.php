<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('transfert_stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_projet_source');
            $table->unsignedBigInteger('id_projet_destination');
            $table->unsignedBigInteger('article_id');
            $table->integer('quantite');
            $table->date('date_transfert');
            $table->timestamps();

            $table->foreign('id_projet_source')->references('id')->on('projets')->onDelete('cascade');
            $table->foreign('id_projet_destination')->references('id')->on('projets')->onDelete('cascade');
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('transfert_stock');
    }
};
