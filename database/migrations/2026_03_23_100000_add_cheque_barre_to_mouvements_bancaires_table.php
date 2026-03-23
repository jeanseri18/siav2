<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mouvements_bancaires', function (Blueprint $table) {
            $table->boolean('cheque_barre')->default(false)->after('numero_piece');
        });
    }

    public function down(): void
    {
        Schema::table('mouvements_bancaires', function (Blueprint $table) {
            $table->dropColumn('cheque_barre');
        });
    }
};
