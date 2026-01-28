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
        Schema::create('facture_decomptes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facture_contrat_id');
            $table->string('numero')->unique();
            $table->date('date_facture');
            $table->decimal('pourcentage_avancement', 5, 2);
            $table->decimal('montant_ht', 15, 2);
            $table->decimal('montant_ttc', 15, 2);
            $table->string('statut')->default('brouillon');
            $table->text('observations')->nullable();
            $table->timestamps();
            
            $table->foreign('facture_contrat_id')->references('id')->on('facture_contrats')->onDelete('cascade');
            $table->index('facture_contrat_id');
            $table->index('numero');
            $table->index('date_facture');
        });

        Schema::create('facture_decompte_lignes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facture_decompte_id');
            $table->unsignedBigInteger('dqe_ligne_id');
            $table->decimal('quantite_realisee', 15, 2);
            $table->decimal('pourcentage_realise', 5, 2);
            $table->decimal('montant_ht', 15, 2);
            $table->text('observations')->nullable();
            $table->timestamps();
            
            $table->foreign('facture_decompte_id')->references('id')->on('facture_decomptes')->onDelete('cascade');
            $table->foreign('dqe_ligne_id')->references('id')->on('dqe_lignes')->onDelete('cascade');
            $table->index(['facture_decompte_id', 'dqe_ligne_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facture_decompte_lignes');
        Schema::dropIfExists('facture_decomptes');
    }
};