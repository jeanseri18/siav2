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
        Schema::table('stock_projet', function (Blueprint $table) {
            $table->unsignedBigInteger('unite_mesure_id')->nullable()->after('quantite');
            $table->foreign('unite_mesure_id')->references('id')->on('unite_mesures')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_projet', function (Blueprint $table) {
            $table->dropForeign(['unite_mesure_id']);
            $table->dropColumn('unite_mesure_id');
        });
    }
};