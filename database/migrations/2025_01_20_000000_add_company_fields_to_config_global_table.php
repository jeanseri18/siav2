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
        Schema::table('config_global', function (Blueprint $table) {
            $table->string('nom_entreprise')->nullable();
            $table->string('localisation')->nullable();
            $table->text('adresse_postale')->nullable();
            $table->string('rccm')->nullable();
            $table->string('cc')->nullable();
            $table->string('tel1')->nullable();
            $table->string('tel2')->nullable();
            $table->string('email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('config_global', function (Blueprint $table) {
            $table->dropColumn(['nom_entreprise', 'localisation', 'adresse_postale', 'rccm', 'cc', 'tel1', 'tel2', 'email']);
        });
    }
};