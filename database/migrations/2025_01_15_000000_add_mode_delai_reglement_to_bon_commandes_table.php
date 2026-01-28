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
        Schema::table('bon_commandes', function (Blueprint $table) {
            $table->string('mode_reglement')->nullable()->after('conditions_paiement');
            $table->string('delai_reglement')->nullable()->after('mode_reglement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bon_commandes', function (Blueprint $table) {
            $table->dropColumn(['mode_reglement', 'delai_reglement']);
        });
    }
};