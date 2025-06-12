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
        // Créer la table pour les déboursés chantier
        Schema::create('debourse_chantier', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->foreignId('projet_id')->nullable()->constrained('projets')->onDelete('set null');
            $table->foreignId('contrat_id')->constrained('contrats')->onDelete('cascade');
            $table->foreignId('dqe_id')->constrained('dqes')->onDelete('cascade');
            $table->decimal('montant_total', 15, 2)->default(0);
            $table->string('statut')->default('brouillon');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Créer la table pour les détails des déboursés chantier
        Schema::create('debourse_chantier_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debourse_chantier_id')->constrained('debourse_chantier')->onDelete('cascade');
            $table->foreignId('dqe_ligne_id')->constrained('dqe_lignes')->onDelete('cascade');
            $table->string('section')->nullable();
            $table->string('designation');
            $table->string('unite');
            $table->decimal('quantite', 10, 2);
            $table->decimal('cout_unitaire_materiaux', 12, 2)->nullable();
            $table->decimal('cout_unitaire_materiel', 12, 2)->nullable();
            $table->decimal('total_materiaux', 12, 2)->nullable();
            $table->decimal('total_materiel', 12, 2)->nullable();
            $table->decimal('montant_total', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debourse_chantier_details');
        Schema::dropIfExists('debourse_chantier');
    }
};
