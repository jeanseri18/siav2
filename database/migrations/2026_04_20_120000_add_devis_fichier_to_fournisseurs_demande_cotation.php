<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fournisseurs_demande_cotation', function (Blueprint $table) {
            $table->string('devis_fichier')->nullable()->after('commentaire');
            $table->string('devis_fichier_nom')->nullable()->after('devis_fichier');
        });
    }

    public function down(): void
    {
        Schema::table('fournisseurs_demande_cotation', function (Blueprint $table) {
            $table->dropColumn(['devis_fichier', 'devis_fichier_nom']);
        });
    }
};
