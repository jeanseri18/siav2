<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE demande_cotations MODIFY statut ENUM('en cours','validée','terminée','annulée') NOT NULL DEFAULT 'en cours'");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::table('demande_cotations')->where('statut', 'validée')->update(['statut' => 'terminée']);
        DB::statement("ALTER TABLE demande_cotations MODIFY statut ENUM('en cours','terminée','annulée') NOT NULL DEFAULT 'en cours'");
    }
};
