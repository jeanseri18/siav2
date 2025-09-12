<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('client_fournisseurs')->onDelete('cascade');
            $table->string('numero_client')->nullable();
            $table->string('nom_client')->nullable();
            $table->text('commentaire')->nullable();
            $table->decimal('total_ht', 15, 2)->default(0);
            $table->decimal('tva', 15, 2)->default(0);
            $table->decimal('total_ttc', 15, 2)->default(0);
            $table->string('statut')->default('En attente');
            $table->boolean('utilise_pour_vente')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devis');
    }
};