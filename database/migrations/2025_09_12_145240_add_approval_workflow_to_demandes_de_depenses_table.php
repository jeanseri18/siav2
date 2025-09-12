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
        Schema::table('demandes_de_depenses', function (Blueprint $table) {
            // Utilisateur qui a créé la demande
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Responsable hiérarchique choisi par l'utilisateur
            $table->foreignId('responsable_hierarchique_id')->nullable()->constrained('users')->onDelete('set null');
            
            // RAF (Responsable Administratif et Financier)
            $table->foreignId('raf_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Statuts d'approbation
            $table->enum('statut_responsable', ['en_attente', 'approuve', 'rejete'])->default('en_attente');
            $table->enum('statut_raf', ['en_attente', 'approuve', 'rejete'])->default('en_attente');
            
            // Dates d'approbation
            $table->timestamp('date_approbation_responsable')->nullable();
            $table->timestamp('date_approbation_raf')->nullable();
            
            // Commentaires
            $table->text('commentaire_responsable')->nullable();
            $table->text('commentaire_raf')->nullable();
            
            // Modifier le champ statut existant pour inclure les nouveaux statuts
            $table->dropColumn('statut');
        });
        
        Schema::table('demandes_de_depenses', function (Blueprint $table) {
            $table->string('statut', 50)->default('en_attente_responsable')->after('raf_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demandes_de_depenses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['responsable_hierarchique_id']);
            $table->dropForeign(['raf_id']);
            $table->dropColumn([
                'user_id',
                'responsable_hierarchique_id', 
                'raf_id',
                'statut_responsable',
                'statut_raf',
                'date_approbation_responsable',
                'date_approbation_raf',
                'commentaire_responsable',
                'commentaire_raf',
                'statut'
            ]);
        });
        
        Schema::table('demandes_de_depenses', function (Blueprint $table) {
            // Restaurer l'ancien enum statut
            $table->enum('statut', ['validée', 'annulée', 'en attente'])->default('en attente');
        });
    }
};
