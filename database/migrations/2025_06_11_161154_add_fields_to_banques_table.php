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
            $table->string('code_banque')->nullable();
            $table->string('iban')->nullable();
            $table->string('code_swift')->nullable();
            $table->string('domiciliation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banques', function (Blueprint $table) {
            $table->dropColumn(['code_banque', 'iban', 'code_swift', 'domiciliation']);
        });
    }
};
