<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('num')->unique();
            $table->unsignedBigInteger('id_prestation')->nullable();
            $table->unsignedBigInteger('id_contrat')->nullable();
            $table->unsignedBigInteger('id_artisan')->nullable();
            
            $table->date('date_emission');
            $table->integer('num_decompte')->nullable(); // Numéro de décompte
            $table->decimal('taux_avancement', 5, 2)->default(0); // En pourcentage
            $table->decimal('montant_ht', 10, 2)->nullable();
            $table->decimal('montant_total', 10, 2)->nullable();
            $table->decimal('ca_realise', 10, 2)->default(0);
            $table->decimal('montant_reglement', 10, 2)->default(0);
            
            // Si tu veux stocker `reste_a_regler`, sinon calcule-le dynamiquement dans le modèle
            $table->decimal('reste_a_regler', 10, 2)->virtualAs('montant_total - montant_reglement');

            $table->enum('statut', ['en attente', 'payée', 'annulée'])->default('en attente');
            
            // Clés étrangères avec `onUpdate('cascade')`
           
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('factures');
    }
};
