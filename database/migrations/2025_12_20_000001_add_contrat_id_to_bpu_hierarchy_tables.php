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
        // Ajouter contrat_id à la table categorierubriques
        Schema::table('categorierubriques', function (Blueprint $table) {
            $table->unsignedBigInteger('contrat_id')->nullable()->after('type');
            $table->foreign('contrat_id')->references('id')->on('contrats')->onDelete('cascade');
        });

        // Ajouter contrat_id à la table souscategorierubriques
        Schema::table('souscategorierubriques', function (Blueprint $table) {
            $table->unsignedBigInteger('contrat_id')->nullable()->after('type');
            $table->foreign('contrat_id')->references('id')->on('contrats')->onDelete('cascade');
        });

        // Ajouter contrat_id à la table rubriques
        Schema::table('rubriques', function (Blueprint $table) {
            $table->unsignedBigInteger('contrat_id')->nullable()->after('type');
            $table->foreign('contrat_id')->references('id')->on('contrats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorierubriques', function (Blueprint $table) {
            $table->dropForeign(['contrat_id']);
            $table->dropColumn('contrat_id');
        });

        Schema::table('souscategorierubriques', function (Blueprint $table) {
            $table->dropForeign(['contrat_id']);
            $table->dropColumn('contrat_id');
        });

        Schema::table('rubriques', function (Blueprint $table) {
            $table->dropForeign(['contrat_id']);
            $table->dropColumn('contrat_id');
        });
    }
};