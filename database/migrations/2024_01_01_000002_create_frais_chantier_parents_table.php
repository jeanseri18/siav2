<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('frais_chantier_parents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contrat_id');
            $table->unsignedBigInteger('dqe_id')->nullable();
            $table->string('type')->default('previsionnelle');
            $table->string('ref')->nullable();
            $table->decimal('montant_total', 15, 2)->default(0);
            $table->string('statut')->default('brouillon');
            $table->timestamps();

            $table->foreign('contrat_id')->references('id')->on('contrats')->onDelete('cascade');
            $table->foreign('dqe_id')->references('id')->on('dqes')->onDelete('cascade');
            $table->index(['contrat_id', 'statut']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('frais_chantier_parents');
    }
};