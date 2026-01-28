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
        Schema::table('dqe_lignes', function (Blueprint $table) {
            // Supprimer la contrainte de clé étrangère si elle existe
            $table->dropForeign(['bpu_id']);
            
            // Supprimer la colonne bpu_id
            $table->dropColumn('bpu_id');
            
            // Ajouter la colonne id_rubrique
            $table->unsignedBigInteger('id_rubrique')->nullable()->after('dqe_id');
            
            // Ajouter la contrainte de clé étrangère
            $table->foreign('id_rubrique')->references('id')->on('rubriques')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dqe_lignes', function (Blueprint $table) {
            // Supprimer la contrainte de clé étrangère
            $table->dropForeign(['id_rubrique']);
            
            // Supprimer la colonne id_rubrique
            $table->dropColumn('id_rubrique');
            
            // Ajouter à nouveau la colonne bpu_id
            $table->unsignedBigInteger('bpu_id')->nullable()->after('dqe_id');
            
            // Ajouter la contrainte de clé étrangère
            $table->foreign('bpu_id')->references('id')->on('bpus')->onDelete('cascade');
        });
    }
};
