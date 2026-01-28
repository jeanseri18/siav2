<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ligne_benefices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dqe_id');
            $table->unsignedBigInteger('id_rubrique');
            $table->string('designation');
            $table->string('unite');
            $table->decimal('quantite', 15, 2);
            $table->decimal('pu_ht', 15, 2);
            $table->decimal('montant_ht', 15, 2)->virtualAs('pu_ht * quantite');
            $table->timestamps();
            
            $table->foreign('dqe_id')->references('id')->on('dqes')->onDelete('cascade');
            $table->foreign('id_rubrique')->references('id')->on('rubriques')->onDelete('cascade');
            $table->index(['dqe_id', 'id_rubrique']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ligne_benefices');
    }
};