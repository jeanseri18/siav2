<?php

// Brouillard de Caisse Migration
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('brouillard_caisses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained('bus')->onDelete('cascade');
            $table->enum('type', ['Entrée', 'Sortie']);
            $table->decimal('montant', 15, 2);
            $table->string('motif');
            $table->decimal('solde_cumule', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('brouillard_caisses');
    }
};
