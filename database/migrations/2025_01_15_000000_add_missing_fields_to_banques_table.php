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
        Schema::table('banques', function (Blueprint $table) {
            $table->string('code_guichet')->nullable()->after('code_banque');
            $table->string('numero_compte')->nullable()->after('code_guichet');
            $table->string('cle_rib')->nullable()->after('numero_compte');
            $table->string('telephone')->nullable()->after('domiciliation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banques', function (Blueprint $table) {
            $table->dropColumn(['code_guichet', 'numero_compte', 'cle_rib', 'telephone']);
        });
    }
};