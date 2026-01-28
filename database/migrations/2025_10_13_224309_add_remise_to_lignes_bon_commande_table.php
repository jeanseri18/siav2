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
            $table->decimal('remise', 5, 2)->default(0)->after('prix_unitaire')->comment('Pourcentage de remise appliqué');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lignes_bon_commande', function (Blueprint $table) {
            $table->dropColumn('remise');
        });
    }
};
