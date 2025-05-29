<?php

// Approvisionnement de Caisse Migration
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('approvisionnements_caisse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained('bus')->onDelete('cascade');
            $table->decimal('montant', 15, 2);
            $table->string('motif');
            $table->enum('statut', ['validé', 'annulé', 'en attente'])->default('en attente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('approvisionnements_caisse');
    }
};
