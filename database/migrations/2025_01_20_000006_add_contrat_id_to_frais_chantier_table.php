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
        Schema::table('frais_chantiers', function (Blueprint $table) {
            $table->unsignedBigInteger('contrat_id')->after('id');
            $table->foreign('contrat_id')->references('id')->on('contrats')->onDelete('cascade');
            $table->index('contrat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frais_chantiers', function (Blueprint $table) {
            $table->dropForeign(['contrat_id']);
            $table->dropColumn('contrat_id');
        });
    }
};