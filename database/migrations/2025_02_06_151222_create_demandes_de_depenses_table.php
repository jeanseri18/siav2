<?php
// Demande de Dépenses Migration
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('demandes_de_depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained('bus')->onDelete('cascade');
            $table->decimal('montant', 15, 2);
            $table->string('motif');
            $table->enum('statut', ['validée', 'annulée', 'en attente'])->default('en attente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('demandes_de_depenses');
    }
};
