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
        // Table principale des DQE
        Schema::create('dqes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained('contrats')->onDelete('cascade');
            $table->string('reference')->nullable();
            $table->decimal('montant_total_ht', 15, 2)->default(0);
            $table->decimal('montant_total_ttc', 15, 2)->default(0);
            $table->string('statut')->default('brouillon'); // brouillon, validé, archivé
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Table des lignes de DQE
        Schema::create('dqe_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dqe_id')->constrained('dqes')->onDelete('cascade');
            $table->foreignId('bpu_id')->constrained('bpus')->onDelete('cascade');
            $table->string('code')->nullable(); // Code optionnel pour les lignes
            $table->string('designation');
            $table->decimal('quantite', 10, 2);
            $table->string('unite');
            $table->decimal('pu_ht', 12, 2);
            $table->decimal('montant_ht', 15, 2);
            $table->timestamps();
        });

        // Table pour les déboursés
        Schema::create('debourses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained('contrats')->onDelete('cascade');
            $table->foreignId('dqe_id')->constrained('dqes')->onDelete('cascade');
            $table->enum('type', ['sec', 'main_oeuvre', 'frais_chantier', 'chantier'])->default('sec');
            $table->decimal('montant_total', 15, 2);
            $table->string('statut')->default('brouillon');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Table pour les détails des déboursés
        Schema::create('debourse_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debourse_id')->constrained('debourses')->onDelete('cascade');
            $table->foreignId('dqe_ligne_id')->constrained('dqe_lignes')->onDelete('cascade');
            $table->decimal('montant', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debourse_details');
        Schema::dropIfExists('debourses');
        Schema::dropIfExists('dqe_lignes');
        Schema::dropIfExists('dqes');
    }
};