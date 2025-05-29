<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategorierubriquesTable extends Migration
{
    public function up()
    {
        Schema::create('categorierubriques', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Nom de la catégorie
            $table->string('type')->default('bpu'); // Type de la catégorie
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('categorierubriques');
    }
}