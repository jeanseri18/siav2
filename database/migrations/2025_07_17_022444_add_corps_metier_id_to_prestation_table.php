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
        Schema::table('prestation', function (Blueprint $table) {
            $table->unsignedBigInteger('corps_metier_id')->nullable()->after('id_contrat');
            $table->foreign('corps_metier_id')->references('id')->on('corp_metiers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prestation', function (Blueprint $table) {
            $table->dropForeign(['corps_metier_id']);
            $table->dropColumn('corps_metier_id');
        });
    }
};
