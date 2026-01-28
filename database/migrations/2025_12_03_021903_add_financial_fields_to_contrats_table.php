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
        Schema::table('contrats', function (Blueprint $table) {
            if (!Schema::hasColumn('contrats', 'ref_contrat')) {
                $table->string('ref_contrat')->nullable();
            }
            if (!Schema::hasColumn('contrats', 'tva_18')) {
                $table->boolean('tva_18')->default(true);
            }
            if (!Schema::hasColumn('contrats', 'retenue_decennale')) {
                $table->decimal('retenue_decennale', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('contrats', 'avance_demarrage')) {
                $table->decimal('avance_demarrage', 15, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn([ 'tva_18', 'retenue_decennale', 'avance_demarrage']);
        });
    }
};
