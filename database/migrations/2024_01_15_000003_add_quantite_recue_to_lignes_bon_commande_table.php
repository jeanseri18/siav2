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
        Schema::table('lignes_bon_commande', function (Blueprint $table) {
            // Ajouter la colonne quantite_recue si elle n'existe pas
            if (!Schema::hasColumn('lignes_bon_commande', 'quantite_recue')) {
                $table->decimal('quantite_recue', 10, 2)->default(0)->after('quantite_livree');
            }
            
            // Ajouter un index pour améliorer les performances
            $table->index(['quantite_recue']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lignes_bon_commande', function (Blueprint $table) {
            if (Schema::hasColumn('lignes_bon_commande', 'quantite_recue')) {
                $table->dropIndex(['quantite_recue']);
                $table->dropColumn('quantite_recue');
            }
        });
    }
};