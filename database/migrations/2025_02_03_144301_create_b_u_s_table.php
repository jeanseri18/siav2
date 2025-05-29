<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('bus', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->foreignId('secteur_activite_id')->constrained('secteur_activites')->onDelete('cascade');
            $table->integer('nombre_utilisateurs')->default(0);
            $table->string('adresse');
            $table->string('logo')->nullable();
            $table->string('numero_rccm');
            $table->string('numero_cc');
            $table->string('soldecaisse')->nullable();
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus');
    }
};
