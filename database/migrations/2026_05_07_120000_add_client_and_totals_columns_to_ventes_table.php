<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Alignement schéma / modèle Vente : le formulaire et VenteController
 * enregistrent numero_client, nom_client, commentaire, total_ht, tva, total_ttc.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            if (! Schema::hasColumn('ventes', 'numero_client')) {
                $table->string('numero_client')->nullable()->after('devis_id');
            }
            if (! Schema::hasColumn('ventes', 'nom_client')) {
                $table->string('nom_client')->nullable()->after('numero_client');
            }
            if (! Schema::hasColumn('ventes', 'commentaire')) {
                $table->text('commentaire')->nullable()->after('nom_client');
            }
            if (! Schema::hasColumn('ventes', 'total_ht')) {
                $table->decimal('total_ht', 15, 2)->nullable()->after('commentaire');
            }
            if (! Schema::hasColumn('ventes', 'tva')) {
                $table->decimal('tva', 15, 2)->nullable()->after('total_ht');
            }
            if (! Schema::hasColumn('ventes', 'total_ttc')) {
                $table->decimal('total_ttc', 15, 2)->nullable()->after('tva');
            }
        });

        if (Schema::hasColumn('ventes', 'total') && Schema::hasColumn('ventes', 'total_ttc')) {
            foreach (DB::table('ventes')->whereNull('total_ttc')->orderBy('id')->get() as $row) {
                $ttc = (float) $row->total;
                $ht = round($ttc / 1.18, 2);
                $tva = round($ttc - $ht, 2);
                DB::table('ventes')->where('id', $row->id)->update([
                    'total_ttc' => $ttc,
                    'total_ht' => $ht,
                    'tva' => $tva,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $cols = [];
            foreach (['total_ttc', 'tva', 'total_ht', 'commentaire', 'nom_client', 'numero_client'] as $c) {
                if (Schema::hasColumn('ventes', $c)) {
                    $cols[] = $c;
                }
            }
            if ($cols !== []) {
                $table->dropColumn($cols);
            }
        });
    }
};
