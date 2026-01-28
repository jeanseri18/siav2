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
        Schema::table('demandes_ravitaillement', function (Blueprint $table) {
            $table->dropForeign(['contrat_id']);
            $table->unsignedBigInteger('contrat_id')->nullable()->change();
            $table->foreign('contrat_id')->references('id')->on('contrats')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demandes_ravitaillement', function (Blueprint $table) {
            $table->dropForeign(['contrat_id']);
            $table->unsignedBigInteger('contrat_id')->nullable(false)->change();
            $table->foreign('contrat_id')->references('id')->on('contrats')->onDelete('cascade');
        });
    }
};