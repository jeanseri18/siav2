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
        Schema::table('debourse_details', function (Blueprint $table) {
            $table->decimal('cout_unitaire_materiaux', 12, 2)->nullable()->after('montant');
            $table->decimal('cout_unitaire_main_oeuvre', 12, 2)->nullable()->after('cout_unitaire_materiaux');
            $table->decimal('cout_unitaire_materiel', 12, 2)->nullable()->after('cout_unitaire_main_oeuvre');
            $table->decimal('total_materiaux', 12, 2)->nullable()->after('cout_unitaire_materiel');
            $table->decimal('total_main_oeuvre', 12, 2)->nullable()->after('total_materiaux');
            $table->decimal('total_materiel', 12, 2)->nullable()->after('total_main_oeuvre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debourse_details', function (Blueprint $table) {
            $table->dropColumn([
                'cout_unitaire_materiaux',
                'cout_unitaire_main_oeuvre', 
                'cout_unitaire_materiel',
                'total_materiaux',
                'total_main_oeuvre',
                'total_materiel'
            ]);
        });
    }
};
