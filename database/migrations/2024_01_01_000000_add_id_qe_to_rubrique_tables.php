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
        Schema::table('categorierubriques', function (Blueprint $table) {
            $table->unsignedBigInteger('id_qe')->nullable()->after('contrat_id');
            $table->foreign('id_qe')->references('id')->on('dqes')->onDelete('set null');
        });
        
        Schema::table('souscategorierubriques', function (Blueprint $table) {
            $table->unsignedBigInteger('id_qe')->nullable()->after('contrat_id');
            $table->foreign('id_qe')->references('id')->on('dqes')->onDelete('set null');
        });
        
        Schema::table('rubriques', function (Blueprint $table) {
            $table->unsignedBigInteger('id_qe')->nullable()->after('contrat_id');
            $table->foreign('id_qe')->references('id')->on('dqes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorierubriques', function (Blueprint $table) {
            $table->dropForeign(['id_qe']);
            $table->dropColumn('id_qe');
        });
        
        Schema::table('souscategorierubriques', function (Blueprint $table) {
            $table->dropForeign(['id_qe']);
            $table->dropColumn('id_qe');
        });
        
        Schema::table('rubriques', function (Blueprint $table) {
            $table->dropForeign(['id_qe']);
            $table->dropColumn('id_qe');
        });
    }
};