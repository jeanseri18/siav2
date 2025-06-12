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
        Schema::table('debourse_chantier_details', function (Blueprint $table) {
            // Ajouter les colonnes pour séparer déboursé sec et main d'œuvre
            $table->decimal('cout_unitaire_main_oeuvre', 12, 2)->nullable()->after('cout_unitaire_materiel');
            $table->decimal('total_main_oeuvre', 12, 2)->nullable()->after('total_materiel');
            
            // Renommer les colonnes existantes pour plus de clarté
            // cout_unitaire_materiaux reste pour les matériaux
            // cout_unitaire_materiel reste pour le matériel
            // total_materiaux reste pour les matériaux
            // total_materiel reste pour le matériel
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debourse_chantier_details', function (Blueprint $table) {
            $table->dropColumn(['cout_unitaire_main_oeuvre', 'total_main_oeuvre']);
        });
    }
};
