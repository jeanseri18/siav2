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
            if (!Schema::hasColumn('projets', 'tva_achat')) {
                $table->boolean('tva_achat')->default(false)->after('hastva');
            }
            if (!Schema::hasColumn('projets', 'montant_global')) {
                $table->decimal('montant_global', 20, 2)->nullable()->after('tva_achat');
            } else {
                $table->decimal('montant_global', 20, 2)->nullable()->after('tva_achat')->change();
            }
            if (!Schema::hasColumn('projets', 'chiffre_affaire_global')) {
                $table->decimal('chiffre_affaire_global', 20, 2)->nullable()->after('montant_global');
            } else {
                $table->decimal('chiffre_affaire_global', 20, 2)->nullable()->after('montant_global')->change();
            }
            if (!Schema::hasColumn('projets', 'total_depenses')) {
                $table->decimal('total_depenses', 20, 2)->nullable()->after('chiffre_affaire_global');
            } else {
                $table->decimal('total_depenses', 20, 2)->nullable()->after('chiffre_affaire_global')->change();
            }
            
            // Information système
            if (!Schema::hasColumn('projets', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('total_depenses');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('projets', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            }
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