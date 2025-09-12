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
            $table->unsignedBigInteger('id_contrat')->nullable()->after('id_projet');
            
            $table->foreign('id_contrat')->references('id')->on('contrats')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_projet', function (Blueprint $table) {
            $table->dropForeign(['id_contrat']);
            $table->dropColumn('id_contrat');
        });
    }
};