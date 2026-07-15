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

        DB::statement("ALTER TABLE projets MODIFY COLUMN statut ENUM('non débuté','en cours','terminé','annulé') NOT NULL DEFAULT 'non débuté'");
        DB::statement("ALTER TABLE contrats MODIFY COLUMN statut ENUM('non débuté','en cours','terminé','annulé') NOT NULL DEFAULT 'non débuté'");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("UPDATE projets SET statut = 'en cours' WHERE statut = 'non débuté'");
        DB::statement("UPDATE contrats SET statut = 'en cours' WHERE statut = 'non débuté'");

        DB::statement("ALTER TABLE projets MODIFY COLUMN statut ENUM('en cours','terminé','annulé') NOT NULL DEFAULT 'en cours'");
        DB::statement("ALTER TABLE contrats MODIFY COLUMN statut ENUM('en cours','terminé','annulé') NOT NULL DEFAULT 'en cours'");
    }
};
