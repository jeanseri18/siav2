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
        Schema::table('projets', function (Blueprint $table) {
            $table->unsignedBigInteger('pays_id')->nullable();
            $table->unsignedBigInteger('ville_id')->nullable();
            $table->unsignedBigInteger('commune_id')->nullable();
            $table->unsignedBigInteger('quartier_id')->nullable();
            $table->unsignedBigInteger('secteur_id')->nullable();
            
            $table->foreign('pays_id')->references('id')->on('pays')->onDelete('set null');
            $table->foreign('ville_id')->references('id')->on('villes')->onDelete('set null');
            $table->foreign('commune_id')->references('id')->on('communes')->onDelete('set null');
            $table->foreign('quartier_id')->references('id')->on('quartiers')->onDelete('set null');
            $table->foreign('secteur_id')->references('id')->on('secteurs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projets', function (Blueprint $table) {
            $table->dropForeign(['pays_id']);
            $table->dropForeign(['ville_id']);
            $table->dropForeign(['commune_id']);
            $table->dropForeign(['quartier_id']);
            $table->dropForeign(['secteur_id']);
            
            $table->dropColumn(['pays_id', 'ville_id', 'commune_id', 'quartier_id', 'secteur_id']);
        });
    }
};
