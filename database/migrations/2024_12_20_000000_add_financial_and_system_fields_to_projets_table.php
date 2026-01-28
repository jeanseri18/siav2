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
        Schema::table('projets', function (Blueprint $table) {
            // Zone financière
            $table->boolean('tva_achat')->default(false)->after('hastva'); // Achat fournisseur soumis à TVA18%
            $table->decimal('montant_global', 15, 2)->nullable()->after('tva_achat'); // Montant global
            $table->decimal('chiffre_affaire_global', 15, 2)->nullable()->after('montant_global'); // Chiffre d'affaire global
            $table->decimal('total_depenses', 15, 2)->nullable()->after('chiffre_affaire_global'); // Total Dépenses
            
            // Information système
            $table->unsignedBigInteger('created_by')->nullable()->after('total_depenses');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            
            // Clés étrangères
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projets', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['tva_achat', 'montant_global', 'chiffre_affaire_global', 'total_depenses', 'created_by', 'updated_by']);
        });
    }
};