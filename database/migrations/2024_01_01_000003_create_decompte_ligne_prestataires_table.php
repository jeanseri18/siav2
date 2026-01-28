<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('decompte_ligne_prestataires', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 10, 2);
            $table->unsignedBigInteger('idprestation');
            $table->date('date');
            $table->decimal('pourcentage_globalpaye', 5, 2);
            $table->timestamps();
            
            $table->foreign('idprestation')->references('id')->on('prestation')->onDelete('cascade');
            
            $table->index('idprestation');
            $table->index('date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('decompte_ligne_prestataires');
    }
};