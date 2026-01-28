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
        Schema::table('demande_achats', function (Blueprint $table) {
            $table->unsignedBigInteger('demande_approvisionnement_id')->nullable()->after('date_demande');
            $table->foreign('demande_approvisionnement_id')
                  ->references('id')
                  ->on('demande_approvisionnements')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demande_achats', function (Blueprint $table) {
            $table->dropForeign(['demande_approvisionnement_id']);
            $table->dropColumn('demande_approvisionnement_id');
        });
    }
};