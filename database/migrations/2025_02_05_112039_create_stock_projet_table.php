<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('stock_projet', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_projet');
            $table->unsignedBigInteger('article_id'); // Référence à l'article
            $table->integer('quantite');
            $table->timestamps();

            $table->foreign('id_projet')->references('id')->on('projets')->onDelete('cascade');
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');

        });
    }

    public function down() {
        Schema::dropIfExists('stock_projet');
    }
};
