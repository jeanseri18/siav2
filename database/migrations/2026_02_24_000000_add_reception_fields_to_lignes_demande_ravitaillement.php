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
        Schema::table('lignes_demande_ravitaillement', function (Blueprint $table) {
            if (!Schema::hasColumn('lignes_demande_ravitaillement', 'quantite_recue')) {
                $table->decimal('quantite_recue', 10, 3)->default(0)->after('quantite_livree');
            }
            if (!Schema::hasColumn('lignes_demande_ravitaillement', 'quantite_retournee')) {
                $table->decimal('quantite_retournee', 10, 3)->default(0)->after('quantite_recue');
            }
            if (!Schema::hasColumn('lignes_demande_ravitaillement', 'retour_valide')) {
                $table->boolean('retour_valide')->default(false)->after('quantite_retournee');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lignes_demande_ravitaillement', function (Blueprint $table) {
            $table->dropColumn(['quantite_recue', 'quantite_retournee', 'retour_valide']);
        });
    }
};