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
        Schema::table('demandes_ravitaillement', function (Blueprint $table) {
            $table->dropForeign(['fournisseur_id']);
            $table->dropColumn(['fournisseur_id', 'montant_estime', 'montant_reel', 'description']);
        });
        
        Schema::table('lignes_demande_ravitaillement', function (Blueprint $table) {
            $table->dropColumn(['prix_unitaire_estime', 'prix_unitaire_reel', 'montant_estime', 'montant_reel', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demandes_ravitaillement', function (Blueprint $table) {
            $table->foreignId('fournisseur_id')->nullable()->constrained('client_fournisseurs')->onDelete('set null');
            $table->decimal('montant_estime', 15, 2)->nullable();
            $table->decimal('montant_reel', 15, 2)->nullable();
            $table->text('description')->nullable();
        });
        
        Schema::table('lignes_demande_ravitaillement', function (Blueprint $table) {
            $table->decimal('prix_unitaire_estime', 15, 2)->nullable();
            $table->decimal('prix_unitaire_reel', 15, 2)->nullable();
            $table->decimal('montant_estime', 15, 2)->nullable();
            $table->decimal('montant_reel', 15, 2)->nullable();
            $table->text('description')->nullable();
        });
    }
};