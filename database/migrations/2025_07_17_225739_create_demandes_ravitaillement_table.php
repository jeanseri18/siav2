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
        Schema::create('demandes_ravitaillement', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('objet');
            $table->text('description')->nullable();
            $table->enum('statut', ['en_attente', 'approuvee', 'rejetee', 'en_cours', 'livree'])->default('en_attente');
            $table->enum('priorite', ['basse', 'normale', 'haute', 'urgente'])->default('normale');
            $table->date('date_demande');
            $table->date('date_livraison_souhaitee')->nullable();
            $table->date('date_livraison_effective')->nullable();
            $table->decimal('montant_estime', 15, 2)->nullable();
            $table->decimal('montant_reel', 15, 2)->nullable();
            $table->text('commentaires')->nullable();
            $table->text('motif_rejet')->nullable();
            
            // Relations
            $table->foreignId('contrat_id')->constrained('contrats')->onDelete('cascade');
            $table->foreignId('demandeur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('approbateur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('fournisseur_id')->nullable()->constrained('client_fournisseurs')->onDelete('set null');
            
            $table->timestamps();
            
            // Index
            $table->index(['statut', 'date_demande']);
            $table->index(['contrat_id', 'statut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes_ravitaillement');
    }
};
